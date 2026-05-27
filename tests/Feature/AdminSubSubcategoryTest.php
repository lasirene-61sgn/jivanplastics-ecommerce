<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminSubSubcategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    #[Test]
    public function admin_can_view_sub_subcategories_list()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.sub_subcategories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.sub_subcategories.index');
    }

    #[Test]
    public function admin_can_view_create_sub_subcategory_form()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.sub_subcategories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.sub_subcategories.create');
    }

    #[Test]
    public function admin_can_create_sub_subcategory()
    {
        $subcategory = Subcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.sub_subcategories.store'), [
                'subcategory_id' => $subcategory->id,
                'name' => 'Test Sub-Subcategory',
                'description' => 'This is a test sub-subcategory',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.sub_subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sub_subcategories', [
            'subcategory_id' => $subcategory->id,
            'name' => 'Test Sub-Subcategory',
            'slug' => 'test-sub-subcategory',
            'description' => 'This is a test sub-subcategory',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_cannot_create_sub_subcategory_with_invalid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.sub_subcategories.store'), [
                'name' => '', // Required field
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_view_edit_sub_subcategory_form()
    {
        $subSubcategory = SubSubcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.sub_subcategories.edit', $subSubcategory));

        $response->assertStatus(200);
        $response->assertViewIs('admin.sub_subcategories.edit');
        $response->assertSee($subSubcategory->name);
    }

    #[Test]
    public function admin_can_update_sub_subcategory()
    {
        $subcategory = Subcategory::factory()->create();
        $subSubcategory = SubSubcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.sub_subcategories.update', $subSubcategory), [
                'subcategory_id' => $subcategory->id,
                'name' => 'Updated Sub-Subcategory',
                'description' => 'This is an updated sub-subcategory',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.sub_subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sub_subcategories', [
            'id' => $subSubcategory->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Updated Sub-Subcategory',
            'description' => 'This is an updated sub-subcategory',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_sub_subcategory()
    {
        $subSubcategory = SubSubcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.sub_subcategories.destroy', $subSubcategory));

        $response->assertRedirect(route('admin.sub_subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('sub_subcategories', [
            'id' => $subSubcategory->id,
        ]);
    }
}