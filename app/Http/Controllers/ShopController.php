<?php

namespace App\Http\Controllers;

use App\Models\Carousel;
use App\Models\FlashSale;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SubCategory;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ShopController extends Controller
{
    public function index()
    {
        if (auth()->user()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (auth()->user()->hasRole('shipper')) {
                return view('shipper.index',['admins' => User::all()]);
            }
        }
        $carousels = Carousel::latest()->take(3)->get();
        $newProducts = Product::inRandomOrder()->with('productImage')->take(24)->get();
        return view('shop.index')->with([
            'carousels' => $carousels,
            'newProducts' => $newProducts,
        ]);
    }
    public function show($id)
    {
        if (auth()->user()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (auth()->user()->hasRole('shipper')) {
                return view('shipper.index',['admins' => User::all()]);
            }
        }        $product = Product::where('id', $id)->with('productImage','category','attributes')->first();
        $product->image = $product->productImage->first();

        $questions = $product->getQuestions()->with('user')->paginate(6);
        $mightAlsoLike = Product::where('id', '!=', $product->id)->inRandomOrder()->with('productImage')->take(6)->get();

        return view('shop.show')->with([
            'product' => $product,
            'questions' => $questions,
            'mightAlsoLike' => $mightAlsoLike
        ]);
    }

    public function catalog(Request $request)
    {

        if (auth()->user()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (auth()->user()->hasRole('shipper')) {
                return view('shipper.index',['admins' => User::all()]);
            }
        }
        //get random sub categories

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                'title',
                'subCategory',
                AllowedFilter::scope('min_price'),
                AllowedFilter::scope('max_price'),
            ])
            ->with('productImage')
            ->paginate(20);

        return view('shop.catalog')->with([
            'productCategories' => [],
            'products' => $products
        ]);
    }
}
