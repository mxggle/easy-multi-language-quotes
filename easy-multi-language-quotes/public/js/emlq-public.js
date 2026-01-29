document.addEventListener('DOMContentLoaded', function () {
    const list = document.querySelectorAll('.emlq-root');
    if (list.length === 0) return;

    list.forEach(root => {
        const frequency = root.dataset.frequency || emlqData.frequency;
        const isPopulated = root.innerHTML.trim() !== '';

        // If frequency is 'always' and we have the quotes list locally, randomize client-side.
        if (frequency === 'always' && emlqData.quotes && Array.isArray(emlqData.quotes) && emlqData.quotes.length > 0) {
            const quotes = emlqData.quotes;
            const lastIndex = getCookie('emlq_last_index');
            let availableIndices = quotes.map((_, index) => index);

            if (quotes.length > 1 && lastIndex !== null) {
                availableIndices = availableIndices.filter(index => index != lastIndex);
            }

            const randomIndex = availableIndices[Math.floor(Math.random() * availableIndices.length)];
            const selectedQuote = quotes[randomIndex];

            // Render quote
            const html = generateQuoteHtml(selectedQuote);
            if (html) {
                root.innerHTML = html;
            }

            // Update cookie
            document.cookie = "emlq_last_index=" + randomIndex + "; path=/; max-age=86400";

            // Reveal quote
            root.classList.add('is-visible');
            return;
        }

        // For Hourly/Daily, if it's already populated by SSR, reveal it
        if (isPopulated) {
            root.classList.add('is-visible');
            return;
        }

        const url = emlqData.apiUrl + '?_t=' + new Date().getTime();
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    root.innerHTML = data.html;
                }
                if (data.index !== null && data.index !== undefined) {
                    document.cookie = "emlq_last_index=" + data.index + "; path=/; max-age=86400";
                }
                root.classList.add('is-visible');
            })
            .catch(err => {
                console.error('EMLQ Error:', err);
            });
    });

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function generateQuoteHtml(quote) {
        const zh = quote.quote && quote.quote.zh ? quote.quote.zh : '';
        const en = quote.quote && quote.quote.en ? quote.quote.en : '';
        const ja = quote.quote && quote.quote.ja ? quote.quote.ja : '';
        const author = quote.author ? quote.author : '';

        let html = '<div class="easy-quotes-container"><div class="easy-quotes-quote">';
        if (zh) html += `<div class="emlq-text emlq-zh">${escapeHtml(zh)}</div>`;
        if (en) html += `<div class="emlq-text emlq-en">${escapeHtml(en)}</div>`;
        if (ja) html += `<div class="emlq-text emlq-ja">${escapeHtml(ja)}</div>`;
        if (author) html += `<div class="emlq-author">- ${escapeHtml(author)}</div>`;
        html += '</div></div>';
        return html;
    }

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
