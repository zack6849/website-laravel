const units = [
    ['year', 31536000000],
    ['month', 2592000000],
    ['week', 604800000],
    ['day', 86400000],
    ['hour', 3600000],
    ['minute', 60000],
    ['second', 1000],
];

const relativeFormatter = new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' });
const timeFormatter = new Intl.DateTimeFormat(undefined, {
    hour: 'numeric',
    minute: '2-digit',
    timeZoneName: 'short',
});

function parse(timestamp) {
    if (timestamp === null || timestamp === undefined || timestamp === '') {
        return null;
    }

    if (typeof timestamp === 'number' || /^\d+$/.test(String(timestamp))) {
        const numericTimestamp = Number(timestamp);
        const milliseconds = numericTimestamp > 9999999999
            ? numericTimestamp
            : numericTimestamp * 1000;
        const date = new Date(milliseconds);

        return Number.isNaN(date.getTime()) ? null : date;
    }

    const date = new Date(String(timestamp));

    return Number.isNaN(date.getTime()) ? null : date;
}

function relativeTime(date) {
    const diffMs = date.getTime() - Date.now();
    const absMs = Math.abs(diffMs);

    if (absMs < 30000) {
        return 'just now';
    }

    const [unit, ms] = units.find(([, ms]) => absMs >= ms) || ['second', 1000];

    return relativeFormatter.format(Math.round(diffMs / ms), unit);
}

function localTime(date) {
    return timeFormatter.format(date);
}

const RelativeUTCTime = {
    parse,

    format(timestamp, options = {}) {
        const date = parse(timestamp);

        if (date === null) {
            return options.fallback || 'Import status unknown';
        }

        const prefix = options.prefix || 'Imported';

        return `${prefix} ${relativeTime(date)} (${localTime(date)})`;
    },

    hydrate(root = document) {
        root.querySelectorAll('[data-relative-utc-time]').forEach((element) => {
            const formatted = this.format(element.getAttribute('data-relative-utc-time'), {
                fallback: element.textContent.trim(),
                prefix: element.getAttribute('data-relative-utc-time-prefix') || 'Imported',
            });

            element.textContent = formatted;
        });
    },
};

export default RelativeUTCTime;
