<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    #[Test]
    public function admin_can_view_categories_list()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    #[Test]
    public function admin_can_view_create_category_form()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
    }

    #[Test]
    public function admin_can_create_category()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'description' => 'This is a test category',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_cannot_create_category_with_invalid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), [
                'name' => '', // Required field
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function admin_can_view_edit_category_form()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
        $response->assertSee($category->name);
    }

    #[Test]
    public function admin_can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Category',
                'description' => 'This is an updated category',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'This is an updated category',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}