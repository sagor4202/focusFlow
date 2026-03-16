import './bootstrap';

import Alpine from 'alpinejs';

window.focusFlowBoard = () => ({
    taskSearch: '',
    statusFilter: 'all',
    matchesTask(searchable, completed) {
        const normalizedSearchable = (searchable ?? '').toLowerCase();
        const normalizedQuery = this.taskSearch.trim().toLowerCase();
        const searchMatches = normalizedQuery === '' || normalizedSearchable.includes(normalizedQuery);

        const statusMatches =
            this.statusFilter === 'all'
            || (this.statusFilter === 'active' && !completed)
            || (this.statusFilter === 'completed' && completed);

        return searchMatches && statusMatches;
    },
});

window.Alpine = Alpine;

Alpine.start();
