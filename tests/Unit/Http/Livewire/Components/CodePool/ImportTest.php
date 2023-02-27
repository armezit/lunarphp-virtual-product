<?php

namespace Armezit\Lunar\VirtualProduct\Tests\Unit\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Import;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Armezit\Lunar\VirtualProduct\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Currency;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class ImportTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        // TODO: keep this until job batches become supported in lunar 0.3
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->text('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
    }

    /** @test */
    public function component_mounts_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(Import::class)
            ->assertViewIs('lunarphp-virtual-product::livewire.components.code-pool.import');
    }

    /** @test */
    public function component_mounts_correctly_with_initial_product()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $product = Product::factory()->create([
            'id' => $this->faker->numberBetween(1, 1000),
        ]);

        $schemaFields = [
            ['name' => 'serial', 'type' => $this->faker->word, 'order' => 1],
            ['name' => 'pin', 'type' => $this->faker->word, 'order' => 2],
        ];

        $codePoolSchema = CodePoolSchema::factory()->create(['fields' => $schemaFields]);

        VirtualProduct::factory()->create([
            'product_id' => $product->id,
            'source' => CodePool::class,
            'meta' => ['schemaId' => $codePoolSchema->id],
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->withQueryParams(['pid' => $product->id])
            ->test(Import::class)
            ->assertSet('productId', $product->id)
            ->assertCount('productVariants', 0)
            ->assertSet('showCsvImporter', false)
            ->assertSet('schemaFields', collect($schemaFields))
            ->assertSet('columnsToMap', ['serial' => '', 'pin' => '']);

        $variants = ProductVariant::factory()
            ->count(2)
            ->create([
                'product_id' => $product->id,
            ]);

        LiveWire::actingAs($staff, 'staff')
            ->withQueryParams(['pid' => $product->id, 'vid' => $variants[0]->id])
            ->test(Import::class)
            ->assertCount('productVariants', 2)
            ->assertSet('showCsvImporter', true)
            ->assertSet('schemaFields', collect($schemaFields))
            ->assertSet('columnsToMap', ['serial' => '', 'pin' => '']);
    }

    /** @test */
    public function can_handle_file_upload_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $invalidFile = UploadedFile::fake()
            ->createWithContent('test.foo', '')
            ->size(500000);

        LiveWire::actingAs($staff, 'staff')
            ->test(Import::class)
            ->set('file', $invalidFile)
            ->assertHasErrors(['file']);

        $validFile = UploadedFile::fake()
            ->createWithContent('test.csv', implode("\n", [
                'SN,PN',
                'SN-01,PN-01',
                'SN-02,PN-02',
            ]));

        LiveWire::actingAs($staff, 'staff')
            ->test(Import::class)
            ->set('file', $validFile)
            ->assertSet('fileHeaders', ['SN', 'PN'])
            ->assertSet('fileRowCount', 2);
    }

    /** @test */
    public function import_data_from_csv_file()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        Currency::factory()->create(['default' => true]);

        $product = Product::factory()->create([
            'id' => $this->faker->numberBetween(1, 1000),
        ]);

        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $codePoolSchema = CodePoolSchema::factory()->create([
            'fields' => [
                ['name' => 'A', 'type' => CodePoolFieldType::Raw->value, 'order' => 1],
                ['name' => 'B', 'type' => CodePoolFieldType::Email->value, 'order' => 2],
                ['name' => 'C', 'type' => CodePoolFieldType::Float->value, 'order' => 3],
            ],
        ]);

        VirtualProduct::factory()->create([
            'product_id' => $product->id,
            'source' => CodePool::class,
            'meta' => ['schemaId' => $codePoolSchema->id],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent('test.csv', implode("\n", [
                'b,a,c',
                'foo@bar.com,foo,14.5',
                ...collect(range(1, 6))
                    ->map(fn () => implode(',', [
                        $this->faker->email,
                        $this->faker->word,
                        $this->faker->randomFloat(random_int(1, 4), 10, 1000),
                    ]))
                    ->toArray(),
            ]));

        LiveWire::actingAs($staff, 'staff')
            ->withQueryParams(['pid' => $product->id, 'vid' => $variant->id])
            ->test(Import::class)
            ->set('file', $file)
            ->set('columnsToMap', [
                'A' => 'a',
                'B' => 'b',
                'C' => 'c',
            ])
            ->call('import')
            ->assertSet('fileRowCount', 7);

        $config = config('lunarphp-virtual-product.code_pool');

        $this->assertDatabaseCount($config['batches_table'], 1);
        $this->assertDatabaseHas($config['batches_table'], [
            'purchasable_id' => $variant->id,
            'staff_id' => Auth::guard('staff')->user()->id,
            'status' => CodePoolBatchStatus::Completed,
        ]);

        $this->assertDatabaseCount($config['items_table'], 7);
        $this->assertDatabaseHas($config['items_table'], [
            'schema_id' => $codePoolSchema->id,
            'data' => json_encode([
                'A' => 'foo',
                'B' => 'foo@bar.com',
                'C' => '14.5',
            ]),
        ]);
        $this->assertDatabaseMissing($config['items_table'], [
            'schema_id' => $codePoolSchema->id,
            'data' => json_encode([
                'B' => 'foo@bar.com',
                'A' => 'foo',
                'C' => '14.5',
            ]),
        ]);
    }
}
