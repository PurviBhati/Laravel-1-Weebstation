{{-- Shared dark storefront styles for checkout and order pages (classes start with ui-). --}}
<style>
    .ui-page {
        --u-peach: #ffd9bf;
        --u-pink: #d86e7c;
        --u-wine: #752027;
        --u-toast: #200b05;
        --u-muted: rgba(255, 217, 191, 0.62);
        --u-border: rgba(255, 217, 191, 0.18);

        width: min(1100px, calc(100% - 32px));
        margin: 0 auto;
        padding: 36px 0 70px;
        color: var(--u-peach);
    }

    .ui-page *,
    .ui-page *::before,
    .ui-page *::after {
        box-sizing: border-box;
    }

    .ui-page h1 {
        margin: 0 0 6px;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .ui-sub {
        margin: 0 0 26px;
        color: var(--u-muted);
        line-height: 1.7;
    }

    .ui-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 28px;
        align-items: start;
    }

    .ui-card {
        margin-bottom: 20px;
        padding: 24px;
        border: 1px solid var(--u-border);
        border-radius: 16px;
        background: rgba(255, 217, 191, 0.05);
    }

    .ui-card h2 {
        margin: 0 0 16px;
        font-size: 1.05rem;
    }

    .ui-sticky {
        position: sticky;
        top: 90px;
    }

    .ui-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 16px;
    }

    .ui-field {
        margin-bottom: 16px;
    }

    .ui-field.full {
        grid-column: 1 / -1;
    }

    .ui-label {
        display: block;
        margin-bottom: 7px;
        color: var(--u-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ui-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 10px;
        background: rgba(32, 11, 5, 0.6);
        color: var(--u-peach);
        font: inherit;
    }

    textarea.ui-input {
        min-height: 90px;
        resize: vertical;
    }

    .ui-input:focus {
        outline: 2px solid var(--u-pink);
        outline-offset: 2px;
    }

    .ui-error {
        margin: 6px 0 0;
        color: #ffb5b5;
        font-size: 0.85rem;
    }

    .ui-errors {
        margin-bottom: 20px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 120, 120, 0.5);
        border-radius: 12px;
        background: rgba(255, 90, 90, 0.12);
        color: #ffc4c4;
        font-size: 0.9rem;
        line-height: 1.7;
    }

    /* Buttons */

    .ui-btn {
        display: flex;
        width: 100%;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        padding: 14px 20px;
        border: 1px solid var(--u-peach);
        border-radius: 10px;
        background: var(--u-peach);
        color: var(--u-toast);
        font: inherit;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ui-btn:hover {
        background: transparent;
        color: var(--u-peach);
    }

    .ui-btn--ghost {
        background: transparent;
        color: var(--u-peach);
    }

    .ui-btn--ghost:hover {
        background: var(--u-peach);
        color: var(--u-toast);
    }

    .ui-btn--auto {
        display: inline-flex;
        width: auto;
        padding: 12px 24px;
    }

    .ui-link {
        padding: 0;
        border: 0;
        background: none;
        color: var(--u-muted);
        font: inherit;
        text-decoration: underline;
        cursor: pointer;
    }

    .ui-link:hover {
        color: var(--u-pink);
    }

    /* Choice cards (payment method) */

    .ui-choice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
        padding: 14px 16px;
        border: 1px solid var(--u-border);
        border-radius: 12px;
        cursor: pointer;
    }

    .ui-choice input {
        margin-top: 4px;
        accent-color: var(--u-pink);
    }

    .ui-choice strong {
        display: block;
        margin-bottom: 2px;
    }

    .ui-choice span {
        color: var(--u-muted);
        font-size: 0.85rem;
    }

    .ui-choice:has(input:checked) {
        border-color: var(--u-peach);
        background: rgba(255, 217, 191, 0.08);
    }

    /* Summary lines */

    .ui-line {
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--u-border);
        font-size: 0.9rem;
    }

    .ui-thumb {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 64px;
        overflow: hidden;
        border-radius: 8px;
        background: linear-gradient(145deg, var(--u-wine), var(--u-toast));
        font-size: 0.8rem;
        font-weight: 900;
    }

    .ui-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ui-line-name {
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .ui-line-meta {
        color: var(--u-muted);
        font-size: 0.8rem;
    }

    .ui-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        color: var(--u-muted);
        font-size: 0.92rem;
    }

    .ui-row.total {
        margin-top: 8px;
        padding-top: 14px;
        border-top: 1px solid var(--u-border);
        color: var(--u-peach);
        font-size: 1.15rem;
        font-weight: 800;
    }

    /* Badges */

    .ui-badge {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid var(--u-border);
        border-radius: 999px;
        color: var(--u-peach);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ui-badge--good {
        border-color: rgba(120, 220, 160, 0.5);
        background: rgba(60, 170, 110, 0.16);
        color: #b8f2d0;
    }

    .ui-badge--warn {
        border-color: rgba(255, 200, 110, 0.5);
        background: rgba(255, 170, 60, 0.12);
        color: #ffd9a0;
    }

    .ui-badge--bad {
        border-color: rgba(255, 120, 120, 0.5);
        background: rgba(255, 90, 90, 0.12);
        color: #ffc4c4;
    }

    /* Table */

    .ui-table-wrap {
        overflow-x: auto;
    }

    .ui-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ui-table th {
        padding: 10px;
        border-bottom: 1px solid var(--u-border);
        color: var(--u-muted);
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ui-table td {
        padding: 14px 10px;
        border-bottom: 1px solid rgba(255, 217, 191, 0.08);
        font-size: 0.92rem;
    }

    .ui-table .num {
        text-align: right;
    }

    .ui-table a {
        color: var(--u-pink);
        font-weight: 700;
    }

    .ui-empty {
        padding: 56px 20px;
        border: 1px solid var(--u-border);
        border-radius: 16px;
        background: rgba(255, 217, 191, 0.04);
        text-align: center;
    }

    .ui-empty h2 {
        margin: 0 0 10px;
        font-size: 1.5rem;
    }

    .ui-empty p {
        max-width: 420px;
        margin: 0 auto 22px;
        color: var(--u-muted);
        line-height: 1.7;
    }

    @media (max-width: 880px) {
        .ui-layout {
            grid-template-columns: 1fr;
        }

        .ui-sticky {
            position: static;
        }
    }

    @media (max-width: 600px) {
        .ui-grid {
            grid-template-columns: 1fr;
        }
    }
</style>