<script setup>
import { ref, computed } from 'vue'

const selectedYear = ref(2026)

const months = [
    { index: 1, label: 'JAN' },
    { index: 2, label: 'FEB' },
    { index: 3, label: 'MAR' },
    { index: 4, label: 'APR' },
    { index: 5, label: 'MAY' },
    { index: 6, label: 'JUN' },
    { index: 7, label: 'JUL' },
    { index: 8, label: 'AUG' },
    { index: 9, label: 'SEP' },
    { index: 10, label: 'OCT' },
    { index: 11, label: 'NOV' },
    { index: 12, label: 'DEC' },
]


const highlights = ref([
    { id: 1, date: '2026-01-14', title: 'Projektstart & Arkitektur', category: 'Arbete' },
    { id: 2, date: '2026-02-18', title: 'Första prototypen i Vue', category: 'Projekt' },
    { id: 3, date: '2026-03-08', title: 'Vinterresa till Åre', category: 'Resa' },
    { id: 4, date: '2026-05-19', title: 'Halvmaraton i Göteborg', category: 'Träning' },
    { id: 5, date: '2026-05-28', title: 'Första kvällen på balkongen', category: 'Livet' },
    { id: 6, date: '2026-07-12', title: 'Segling i skärgården', category: 'Resa' },
    { id: 7, date: '2026-08-22', title: 'Sensommarkväll vid havet', category: 'Livet' },
    { id: 8, date: '2026-09-04', title: 'Lansering av v1.0', category: 'Arbete' },
    { id: 9, date: '2026-11-20', title: 'Tidslinjen tar form', category: 'Projekt' },
])


function parseDateParts(isoDate) {
    const [year, month, day] = isoDate.split('-').map(Number)
    return { year, month, day }
}


const highlightsByMonth = computed(() => {
    const grouped = {}
    for (let m = 1; m <= 12; m++) {
        grouped[m] = []
    }

    highlights.value.forEach((item) => {
        const { year, month, day } = parseDateParts(item.date)
        if (year === selectedYear.value && grouped[month]) {
            const monthLabel = months[month - 1]?.label || ''
            grouped[month].push({
                ...item,
                day,
                formattedDate: `${String(day).padStart(2, '0')} ${monthLabel}`,
            })
        }
    })

    Object.keys(grouped).forEach((m) => {
        grouped[m].sort((a, b) => a.day - b.day)
    })

    return grouped
})
</script>

<template>
    <main class="timeline-page">
        <header class="timeline-header">
            <div class="title-group">
<!--                <span class="eyebrow">Overview</span>-->
                <h1>{{ selectedYear }}
                    <span class="overview">OVERVIEW</span>
                </h1>
            </div>
            <div class="actions">
                <button class="btn-ghost" aria-label="New highlight">+</button>
            </div>
        </header>

        <section class="timeline-scroll-container">
            <div class="timeline-grid">
                <div class="axis-line"></div>

                <div
                    v-for="(month, idx) in months"
                    :key="month.index"
                    class="month-column"
                    :class="idx % 2 === 0 ? 'is-top' : 'is-bottom'"
                >
                    <div class="month-marker">
                        <span class="month-label">{{ month.label }}</span>
                    </div>

                    <div
                        v-if="highlightsByMonth[month.index].length"
                        class="stem-wrapper"
                    >
                        <div class="axis-dot"></div>
                        <div class="stem-line"></div>

                        <div class="items-stack">
                            <article
                                v-for="item in highlightsByMonth[month.index]"
                                :key="item.id"
                                class="highlight-item"
                            >
                                <div class="item-node"></div>
                                <div class="item-content">
                                    <span class="item-date">{{ item.formattedDate }}</span>
                                    <h3 class="item-title">{{ item.title }}</h3>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
.timeline-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 2.5rem 3.5rem;
    color: rgba(255, 255, 255, 0.92);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2rem;
}

.eyebrow {
    display: block;
    font-size: 0.7rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.45);
    margin-bottom: 0.35rem;
}

.timeline-header h1 {
    font-size: 2rem;
    font-weight: 400;
    letter-spacing: -0.02em;
    margin: 0;
}

.overview {
    color: rgba(255, 255, 255, 0.45);
}

.btn-ghost {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.85);
    padding: 0.55rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-ghost:hover {
    background: rgba(255, 255, 255, 0.09);
    border-color: rgba(255, 255, 255, 0.25);
}


.timeline-scroll-container {
    flex: 1;
    display: flex;
    align-items: center;
    overflow-x: auto;
    padding: 2rem 0;
}

.timeline-grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(12, minmax(110px, 1fr));
    width: 100%;
    min-width: 1200px;
    height: 540px;
}


.axis-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.04) 0%,
        rgba(255, 255, 255, 0.18) 15%,
        rgba(255, 255, 255, 0.18) 85%,
        rgba(255, 255, 255, 0.04) 100%
    );
}

.month-column {
    position: relative;
    height: 100%;
}

.month-marker {
    position: absolute;
    top: calc(50% + 10px);
    left: 0.6rem;
    background: transparent;
    padding: 0 0.35rem;
    z-index: 1;
}

.month-column.is-bottom .month-marker {
    top: auto;
    bottom: calc(50% + 10px);
}

.month-label {
    font-size: 0.65rem;
    letter-spacing: 0.14em;
    color: rgba(255, 255, 255, 0.35);
}

.stem-wrapper {
    position: absolute;
    left: 1.25rem;
    display: flex;
    flex-direction: column;
    z-index: 2;
}

.month-column.is-top .stem-wrapper {
    bottom: 50%;
    flex-direction: column-reverse;
}

.month-column.is-bottom .stem-wrapper {
    top: 50%;
    flex-direction: column;
}

.axis-dot {
    position: absolute;
    left: 0;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #67e8f9;
    box-shadow: 0 0 8px rgba(103, 232, 249, 0.55);
    z-index: 3;
}

.month-column.is-top .axis-dot {
    bottom: 0;
    transform: translate(-50%, 50%);
}

.month-column.is-bottom .axis-dot {
    top: 0;
    transform: translate(-50%, -50%);
}

.stem-line {
    width: 0;
    height: 48px;
    border-left: 1px dashed rgba(255, 255, 255, 0.22);
    margin-left: -0.5px;
}

.items-stack {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    padding: 0.35rem 0;
}

.month-column.is-top .items-stack {
    flex-direction: column-reverse;
}

.highlight-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    margin-left: -5px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.highlight-item:hover {
    transform: scale(1.02);
}

.item-node {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(148, 185, 225, 0.08);
    border: 1px solid rgba(180, 210, 245, 0.35);
    flex-shrink: 0;
    margin-top: 14px;
    transition: all 0.2s ease;
}

.highlight-item:hover .item-node {
    background: rgba(180, 210, 245, 0.28);
    border-color: rgba(255, 255, 255, 0.85);
}

.item-content {
    width: 165px;
}

.item-date {
    display: block;
    font-size: 0.60rem;
    letter-spacing: 0.12em;
    color: rgba(165, 198, 235, 0.45);
    margin-bottom: 0.1rem;
    transition: color 0.2s ease;
}

.highlight-item:hover .item-date {
    color:  #67e8f9;
}

.item-title {
    font-size: 0.80rem;
    font-weight: 300;
    line-height: 1.4;
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    transition: color 0.2s ease;
}

.highlight-item:hover .item-title {
    color: rgba(255, 255, 255, 0.96);
}
</style>
