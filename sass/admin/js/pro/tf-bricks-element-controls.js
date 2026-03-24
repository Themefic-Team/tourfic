(function () {
    // Wait for Bricks builder to be ready
    if (typeof window.bricksData === 'undefined') return;

    // Hook into Vue reactivity via the Bricks store
    // Bricks uses Vue 3 under the hood — watch for settings changes
    document.addEventListener('DOMContentLoaded', function () {
console.log('xxx');
        const unwatch = setInterval(function () {
            if (!window.bricks?.store) return;
            clearInterval(unwatch);

            bricks.store.watch(
                (state) => state.activeElement?.settings,
                (settings) => {
                    if (!settings) return;

                    const service = settings.service;
                    if (!service) return;

                    // Build the design key from the service name e.g. tf_hotel -> design_hotel
                    const designKey = 'design_' + service.replace('tf_', '');
                    const design    = settings[designKey] || '';

                    // Update the derived tf_context control
                    bricks.store.commit('updateElementSettings', {
                        key:   'tf_context',
                        value: service + ':' + design,
                    });
                },
                { deep: true }
            );
        }, 100);
    });
})();