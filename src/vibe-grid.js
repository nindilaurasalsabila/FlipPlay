const API_URL =
    "https://api.tenrai.org/v1/anime?limit=100&order_by=score&sort=desc";

const animeGrid = document.querySelector("#animeGrid");
const searchInput = document.querySelector("#searchInput");
const loadingMessage = document.querySelector("#loadingMessage");
const errorMessage = document.querySelector("#errorMessage");
const errorText = document.querySelector("#errorText");
const retryButton = document.querySelector("#retryButton");
const emptyMessage = document.querySelector("#emptyMessage");
const resultCount = document.querySelector("#resultCount");
const filterDescription = document.querySelector("#filterDescription");
const auraButtons = document.querySelectorAll(".aura-button");

let animeList = [];
let selectedAura = "all";
let dataReady = false;

function setFeaturesEnabled(enabled) {
    searchInput.disabled = !enabled;

    auraButtons.forEach(button => {
        button.disabled = !enabled;
        button.setAttribute("aria-disabled", String(!enabled));

        if (enabled) {
            button.classList.remove(
                "opacity-50",
                "cursor-not-allowed"
            );
        } else {
            button.classList.add(
                "opacity-50",
                "cursor-not-allowed"
            );
        }
    });
}

function updateFilterDescription() {
    const descriptions = {
        all: "Menampilkan semua anime.",
        energy: "High Energy: anime dengan genre Action atau Adventure.",
        melancholy: "Melancholy: anime dengan genre Drama atau Romance.",
        binge: "Binge Mode: anime dengan maksimal 24 episode."
    };

    filterDescription.textContent = descriptions[selectedAura];
}

function clearAnimeDisplay(message) {
    dataReady = false;
    animeList = [];

    animeGrid.innerHTML = "";
    resultCount.textContent = "";
    emptyMessage.classList.add("hidden");
    loadingMessage.classList.add("hidden");

    setFeaturesEnabled(false);

    errorText.textContent = message;
    errorMessage.classList.remove("hidden");
}

function handleOffline() {
    clearAnimeDisplay(
        "Koneksi internet terputus. Hubungkan kembali internet untuk melihat anime."
    );
}

async function fetchAnime() {
    if (!navigator.onLine) {
        handleOffline();
        return;
    }

    dataReady = false;
    setFeaturesEnabled(false);

    loadingMessage.classList.remove("hidden");
    errorMessage.classList.add("hidden");
    emptyMessage.classList.add("hidden");

    resultCount.textContent = "";
    animeGrid.innerHTML = "";

    try {
        const response = await fetch(API_URL);

        if (!response.ok) {
            throw new Error(
                "Server mengembalikan status " + response.status
            );
        }

        const result = await response.json();

        if (!navigator.onLine) {
            handleOffline();
            return;
        }

        if (!Array.isArray(result.data)) {
            throw new Error("Format data anime tidak sesuai.");
        }

        animeList = result.data;
        dataReady = true;

        errorMessage.classList.add("hidden");
        setFeaturesEnabled(true);

        renderAnime();

    } catch (error) {
        console.error("Gagal mengambil data anime:", error);

        clearAnimeDisplay(
            navigator.onLine
                ? "Data anime gagal dimuat. Periksa koneksi internet atau server, lalu coba lagi."
                : "Koneksi internet terputus. Hubungkan kembali internet untuk melihat anime."
        );

    } finally {
        loadingMessage.classList.add("hidden");
    }
}
function filterByAura(anime) {
    const genres = (anime.genres || []).map(genre =>
        genre.name.toLowerCase()
    );

    if (selectedAura === "energy") {
        return genres.includes("action") ||
               genres.includes("adventure");
    }

    if (selectedAura === "melancholy") {
        return genres.includes("drama") ||
               genres.includes("romance");
    }

    if (selectedAura === "binge") {
        return anime.episodes !== null &&
               anime.episodes !== undefined &&
               anime.episodes <= 24;
    }

    return true;
}

function renderAnime() {
    if (!navigator.onLine) {
        handleOffline();
        return;
    }

    if (!dataReady) {
        return;
    }

    const keyword = searchInput.value.trim().toLowerCase();
    
    const filteredAnime = animeList.filter(anime => {
        const title = (
            anime.title_english || anime.title || ""
        ).toLowerCase();

        return title.includes(keyword) && filterByAura(anime);
    });

    resultCount.textContent =
        `Menampilkan ${filteredAnime.length} dari ${animeList.length} anime`;

    emptyMessage.classList.toggle(
        "hidden",
        filteredAnime.length !== 0
    );

    animeGrid.innerHTML = filteredAnime.map(anime => {
        const title = anime.title_english || anime.title || "Tanpa judul";

        const image =
            anime.images?.jpg?.large_image_url ||
            anime.images?.jpg?.image_url ||
            "";

        const synopsis = anime.synopsis ||
            "Belum ada sinopsis untuk anime ini.";

        const shortSynopsis = synopsis.length > 130
            ? synopsis.slice(0, 130) + "..."
            : synopsis;

        const score = anime.score ?? "Belum ada skor";
        const episodes = anime.episodes ?? "Belum diketahui";

        return `
            <article class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900 transition duration-300 hover:-translate-y-1 hover:border-pink-400/60">
                <img
                    src="${image}"
                    alt="${title}"
                    loading="lazy"
                    class="h-64 w-full bg-slate-800 object-cover"
                >

                <div class="p-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <span class="rounded-full bg-pink-500/15 px-3 py-1 text-xs font-bold text-pink-300">
                            ⭐ ${score}
                        </span>

                        <span class="text-xs text-slate-400">
                            ${episodes} episode
                        </span>
                    </div>

                    <h3 class="mb-3 text-lg font-bold leading-snug">
                        ${title}
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-400">
                        ${shortSynopsis}
                    </p>
                </div>
            </article>
        `;
    }).join("");
}

searchInput.addEventListener("input", () => {
    renderAnime();
});

auraButtons.forEach(button => {
    button.addEventListener("click", () => {
        if (!navigator.onLine || !dataReady) {
            return;
        }

        selectedAura = button.dataset.aura;

        auraButtons.forEach(item => {
            item.classList.remove("bg-pink-500", "text-white");
            item.classList.add(
                "border",
                "border-white/15",
                "bg-slate-900"
            );
        });

        button.classList.add("bg-pink-500", "text-white");
        button.classList.remove(
            "border",
            "border-white/15",
            "bg-slate-900"
        );

        updateFilterDescription();
        renderAnime();
    });
});

retryButton.addEventListener("click", fetchAnime);

window.addEventListener("offline", handleOffline);

window.addEventListener("online", fetchAnime);

updateFilterDescription();
setFeaturesEnabled(false);
fetchAnime();
