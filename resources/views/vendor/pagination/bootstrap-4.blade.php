@if ($paginator->hasPages())
    <nav>
        <div class="d-flex justify-content-center">
            <ul class="pagination">
                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link btn btn-secondary mx-1" aria-hidden="true">...</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link btn btn-primary mx-1">{{ $page }}</span></li>
                            @elseif ($page == $paginator->currentPage() - 1)
                                <li class="page-item"><a class="page-link btn btn-primary mx-1" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Précédent">Précédent</a></li>
                            @elseif ($page == $paginator->lastPage())
                                <li class="page-item"><a class="page-link btn btn-primary mx-1" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Suivant">Suivant</a></li>
                            @else
                                <li class="page-item"><a class="page-link btn btn-secondary mx-1" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
                        @endforeach
            </ul>
        </div>
    </nav>
@endif
