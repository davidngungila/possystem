@if($paginator->hasPages())
<div class="table-pagination">
    <div class="pager-info">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }} — {{ $paginator->total() }} total</div>
    <div class="pager-pages">
        @if($paginator->onFirstPage())
            <span class="pager-btn disabled">‹</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}">‹</a>
        @endif
        @foreach($paginator->getUrlRange(max(1,$paginator->currentPage()-2), min($paginator->lastPage(), $paginator->currentPage()+2)) as $page=>$url)
            @if($page==$paginator->currentPage())
                <span class="pager-btn active">{{ $page }}</span>
            @else
                <a class="pager-btn" href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach
        @if($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}">›</a>
        @else
            <span class="pager-btn disabled">›</span>
        @endif
    </div>
</div>
@endif
