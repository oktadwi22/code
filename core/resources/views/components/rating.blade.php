@props([
    'style' => 1,
    'value' => 0,
    'total_review' => 0
])

<div class="rating-list">
    @php echo displayRating($value) @endphp

    @if ($style === 1)
        <span class="rating-list__rating"> ({{ getAmount($value, 1) }}) </span>
    @elseif($style == 2)
        <span class="rating-list__rating">({{ $total_review }})</span>
    @endif
</div>
