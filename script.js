document.addEventListener('DOMContentLoaded', () => {
    const surahListElement = document.getElementById('surahList');
    const surahNav = document.getElementById('surahNav');
    const juzNav = document.getElementById('juzNav');
    const revelationNav = document.getElementById('revelationNav');
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    let originalSurahs = [];

    // Event listener for Enter key press in the search input field
    searchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            handleSearch();
        }
    });

    // Event listener for search button click
    searchButton.addEventListener('click', handleSearch);

    // Event listener for Ordre de revelation navigation
    revelationNav.addEventListener('click', () => {
        surahNav.classList.remove('active');
        juzNav.classList.remove('active');
        revelationNav.classList.add('active');
        populateSurahsByRevelationOrder(originalSurahs);
    });

    // Load Quran data and populate surahs initially
    fetch('quran.json')
        .then(response => response.json())
        .then(data => {
            originalSurahs = data.sourates;
            populateSurahs(originalSurahs);
        })
        .catch(error => console.error('Error loading Quran data: ', error));

    function handleSearch() {
        const searchInputValue = searchInput.value.toLowerCase();
        const filteredSurahs = originalSurahs.filter(surah => {
            return surah.nom_phonetique.toLowerCase().includes(searchInputValue);
        });
        populateSurahs(filteredSurahs);
    }

    function populateSurahsByJuz(surahs) {
        surahListElement.innerHTML = '';
        const surahsByJuz = {};
        surahs.forEach(surah => {
            const juzNumber = surah.versets[0].juz;
            if (!surahsByJuz[juzNumber]) {
                surahsByJuz[juzNumber] = [];
            }
            surahsByJuz[juzNumber].push(surah);
        });

        Object.keys(surahsByJuz).forEach(juzNumber => {
            const surahsInJuz = surahsByJuz[juzNumber];
            const juzTable = document.createElement('table');
            const juzTitleRow = document.createElement('tr');
            const juzTitleCell = document.createElement('th');
            juzTitleCell.colSpan = 2;
            juzTitleCell.textContent = `Juz ${juzNumber}`;
            juzTitleRow.appendChild(juzTitleCell);
            juzTable.appendChild(juzTitleRow);

            surahsInJuz.forEach(surah => {
                const surahRow = document.createElement('tr');
                const surahPositionCell = document.createElement('td');
                const surahNameCell = document.createElement('td');
                surahPositionCell.textContent = surah.position;
                surahNameCell.textContent = surah.nom_phonetique;
                surahRow.appendChild(surahPositionCell);
                surahRow.appendChild(surahNameCell);
                surahRow.addEventListener('click', () => {
                    sessionStorage.setItem('selectedSurah', JSON.stringify(surah));
                    window.location.href = '../../surat/1.html';
                });

                juzTable.appendChild(surahRow);
            });

            surahListElement.appendChild(juzTable);
        });
    }

    function populateSurahsByRevelationOrder(surahs) {
        surahs.sort((a, b) => a.order - b.order);
        surahListElement.innerHTML = '';

        surahs.forEach(surah => {
            const surahElement = document.createElement('div');
            surahElement.classList.add('surah-item');
            surahElement.innerHTML = `<span class="surah-position">${surah.order}</span> ${surah.nom_phonetique}`;

            surahElement.addEventListener('click', () => {
                sessionStorage.setItem('selectedSurah', JSON.stringify(surah));
                window.location.href = '../../surat/1.html';
            });

            surahListElement.appendChild(surahElement);
        });
    }

    function populateSurahs(surahs) {
        surahListElement.innerHTML = '';

        surahs.forEach(surah => {
            const surahElement = document.createElement('div');
            surahElement.classList.add('surah-item');
            surahElement.innerHTML = `<span class="surah-position">${surah.position}</span> ${surah.nom_phonetique}`;

            surahElement.addEventListener('click', () => {
                sessionStorage.setItem('selectedSurah', JSON.stringify(surah));
                window.location.href = '../../surat/1.html';
            });

            surahListElement.appendChild(surahElement);
        });
    }

    // Event listener for Juz navigation
    juzNav.addEventListener('click', () => {
        surahNav.classList.remove('active');
        juzNav.classList.add('active');
        revelationNav.classList.remove('active');
        populateSurahsByJuz(originalSurahs);
    });

    // Event listener for Surah navigation
    surahNav.addEventListener('click', () => {
        surahNav.classList.add('active');
        juzNav.classList.remove('active');
        revelationNav.classList.remove('active');
        populateSurahs(originalSurahs);
    });
});
