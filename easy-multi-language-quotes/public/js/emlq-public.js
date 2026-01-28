document.addEventListener('DOMContentLoaded', function () {
    const list = document.querySelectorAll('.emlq-root');
    if (list.length === 0) return;

    list.forEach(root => {
        // Add timestamp to prevent browser caching
        const url = emlqData.apiUrl + '?_t=' + new Date().getTime();

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    root.innerHTML = data.html;
                }
                // Update cookie if index is provided
                if (data.index !== null && data.index !== undefined) {
                    document.cookie = "emlq_last_index=" + data.index + "; path=/; max-age=86400";
                }
            })
            .catch(err => {
                console.error('EMLQ Error:', err);
            });
    });
});
