<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminSubcategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    #[Test]
    public function admin_can_view_subcategories_list()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subcategories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subcategories.index');
    }

    #[Test]
    public function admin_can_view_create_subcategory_form()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subcategories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subcategories.create');
    }

    #[Test]
    public function admin_can_create_subcategory()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subcategories.store'), [
                'category_id' => $category->id,
                'name' => 'Test Subcategory',
                'description' => 'This is a test subcategory',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subcategories', [
            'category_id' => $category->id,
            'name' => 'Test Subcategory',
            'slug' => 'test-subcategory',
            'description' => 'This is a test subcategory',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_cannot_create_subcategory_with_invalid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subcategories.store'), [
                'name' => '', // Required field
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_view_edit_subcategory_form()
    {
        $subcategory = Subcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subcategories.edit', $subcategory));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subcategories.edit');
        $response->assertSee($subcategory->name);
    }

    #[Test]
    public function admin_can_update_subcategory()
    {
        $category = Category::factory()->create();
        $subcategory = Subcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.subcategories.update', $subcategory), [
                'category_id' => $category->id,
                'name' => 'Updated Subcategory',
                'description' => 'This is an updated subcategory',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'category_id' => $category->id,
            'name' => 'Updated Subcategory',
            'description' => 'This is an updated subcategory',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_subcategory()
    {
        $subcategory = Subcategory::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.subcategories.destroy', $subcategory));

        $response->assertRedirect(route('admin.subcategories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('subcategories', [
            'id' => $subcategory->id,
        ]);
    }
}