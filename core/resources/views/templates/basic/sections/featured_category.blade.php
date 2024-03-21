@php
    $categories = App\Models\Category::active()->withCount([
        'products' => function ($query) {
            $query->allActive()->approved()->with('author');
        },
    ])
        ->featured()
        ->orderByDesc('products_count')
        ->get();
@endphp
@if ($categories->count())
    <section class="category pt-120 pb-60">
        <div class="container">
            <div class="category__inner">
                <div class="category-item-slider">
                    @foreach ($categories as $category)
                        <div class="category-item">
                            <div class="category-item__thumb">
                                <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}" alt="{{ __(@$category->name) }}" />
                            </div>
                            <div class="category-item__content">
                                <h6 class="category-item__title">
                                    <a href="{{ route('products', ['category' => $category->id]) }}" class="link">
                                        {{ __($category->name) }}
                                    </a>
                                </h6>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
