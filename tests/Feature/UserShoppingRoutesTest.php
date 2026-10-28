<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserShoppingRoutesTest extends TestCase
{
    public function test_user_cart_url_resolves_to_the_cart_page_route(): void
    {
        $route = Route::getRoutes()->match(Request::create('/user/cart'));

        $this->assertSame('user.cart.index', $route->getName());
    }

    public function test_user_wishlist_url_resolves_to_the_wishlist_page_route(): void
    {
        $route = Route::getRoutes()->match(Request::create('/user/wishlist'));

        $this->assertSame('user.wishlist.index', $route->getName());
    }

    public function test_category_and_product_listing_urls_resolve_to_their_own_routes(): void
    {
        $categoryRoute = Route::getRoutes()->match(Request::create('/shop/categories/men'));
        $productListingRoute = Route::getRoutes()->match(Request::create('/shop/men?category=42'));

        $this->assertSame('store.index', $categoryRoute->getName());
        $this->assertSame('shop.index', $productListingRoute->getName());
    }

    public function test_storefront_shortcuts_redirect_to_existing_listing_routes(): void
    {
        $this->get('/shop')->assertRedirect('/shop/everyone');
        $this->get('/shop/categories')->assertRedirect('/shop/categories/everyone');
    }

    public function test_customer_profile_routes_resolve_to_the_profile_actions(): void
    {
        $profileRoute = Route::getRoutes()->match(Request::create('/account/profile'));
        $updateRoute = Route::getRoutes()->match(Request::create('/account/profile', 'PUT'));

        $this->assertSame('account.profile', $profileRoute->getName());
        $this->assertSame('account.update', $updateRoute->getName());

        $this->get('/account/profile')->assertRedirect('/account/login');
        $this->put('/account/profile')->assertRedirect('/account/login');
    }

    public function test_admin_and_customer_order_routes_generate_separate_urls(): void
    {
        $this->assertSame('/user/orders', route('orders.index', [], false));
        $this->assertSame('/admin/orders', route('admin.orders.index', [], false));

        $adminOrderRoute = Route::getRoutes()->match(Request::create('/admin/orders/1'));

        $this->assertSame('admin.orders.show', $adminOrderRoute->getName());
        $this->assertSame(
            'App\Http\Controllers\Admin\AdminOrderController@show',
            $adminOrderRoute->getActionName(),
        );
    }

    public function test_customer_and_admin_order_pages_redirect_to_their_own_login_pages(): void
    {
        $this->get('/user/orders')->assertRedirect('/account/login');
        $this->get('/admin/orders')->assertRedirect('/login');
    }
}
