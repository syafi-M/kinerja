    <script>
        (function() {
            const searchInput = document.getElementById('searchInput');
            const searchTable = document.getElementById('searchTable');

            if (!searchInput || !searchTable) return;
            if (searchInput.dataset.searchMode === 'server') return;

            const tableBody = searchTable.tBodies && searchTable.tBodies.length ? searchTable.tBodies[0] : null;
            if (!tableBody) return;

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const emptyRow = tableBody.querySelector('[data-search-empty-row]') || null;
            let debounceTimer = null;

            function applySearch() {
                const keyword = (searchInput.value || '').trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    if (emptyRow && row === emptyRow) return;

                    const text = (row.textContent || '').toLowerCase();
                    const isMatch = keyword === '' || text.includes(keyword);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) visibleCount += 1;
                });

                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(applySearch, 180);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    applySearch();
                }
            });
        })();
    </script>

