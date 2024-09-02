class PodcastSeries {
  constructor(element) {
    this.element = element;
    this.seasonContainer = this.element.querySelector('.podcast-series__seasons--container');
    this.seasonTabs = this.element.querySelector('.podcast-series__seasons');
    this.episodeList = this.element.querySelector('.podcast-series__episodes');
    this.episodes = this.element.querySelectorAll('.series__card');
    this.associatedList = document.querySelector('.associated-with__list');
    this.authors = document.querySelectorAll('.associated-with__author');
    this.episodeToggle = this.element.querySelector('.podcast-series__show-more');

    this.createTabs();
    this.displayEpisodes();
  }

  createTabs() {
    if (this.episodes.length) {
      const seasonArr = [];
      this.episodes.forEach(episode => {
        const episodeSeason = episode.dataset?.season;
        if (episodeSeason) {
          seasonArr.push(episodeSeason);
        }
      });
      const allSeasons = [...new Set(seasonArr)];

      if (allSeasons.length) {
        for (let i = 0; i < allSeasons.length; i++) {
          const allEpisodes = document.querySelectorAll(`.series__card[data-season="${allSeasons[i]}"]`).length;
          const seasonMarkup = `<li class="season-tab${i === allSeasons.length - 1 ? ' active-season"' : ''}" title="Season ${allSeasons[i]} (${allEpisodes})" data-season="${allSeasons[i]}">Season ${allSeasons[i]} <span>(${allEpisodes})</span></li>`;
          this.seasonTabs.insertAdjacentHTML('afterbegin', seasonMarkup);
        }

        this.addClick();
        this.toggleSeasons(`${allSeasons.length}`);
        this.listenElement();
      }
    }
  }

  addClick() {
    const allTabs = document.querySelectorAll('.season-tab');
    if (allTabs.length > 1) {
      allTabs.forEach(tab => {
        tab.addEventListener('click', () => {
          if (tab.classList.contains('active-season')) return;

          document.querySelector('.active-season').classList.remove('active-season');
          tab.classList.add('active-season');
          this.episodeList.classList.add('podcast-series__truncated');

          this.toggleSeasons(tab.dataset.season);
        });
      });
    }
  }

  toggleSeasons(seasonNum) {
    this.resetToggles(seasonNum);

    const dynamicSeasonContent = [...this.episodes, ...this.authors];

    dynamicSeasonContent.forEach(card => {
      if (card.dataset.season === seasonNum || card.dataset.season === '') {
        card.style.display = 'var(--display)';
      } else {
        card.style.display = 'none';
      }
    });

    // change grid-col variable to keep authors centered when < 4
    if (this.associatedList) {
      const authorCount = document.querySelectorAll(`.associated-with__author[data-season="${seasonNum}"], .associated-with__author[data-season=""]`).length;
      if (authorCount) {
        const gridCol = authorCount < 4 ? authorCount : 4;
        this.associatedList.setAttribute('style', `--grid-col: ${gridCol}`);
      }
    }
  }

  resetToggles(seasonNum) {
    if (document.querySelectorAll(`.series__card[data-season="${seasonNum}"]`).length < 3) {
      this.episodeToggle.style.display = 'none';
    } else {
      this.episodeToggle.removeAttribute('style');
      this.episodeToggle.querySelector('a').textContent = 'View All Podcasts';
    }

    const allChecks = this.episodeList.querySelectorAll('input[type="checkbox"]');
    if (allChecks.length) {
      allChecks.forEach(checkbox => {
        checkbox.checked = false;
      });
    }
  }

  displayEpisodes() {
    this.episodeToggle.addEventListener('click', () => {
      if (this.episodeList.classList.contains('podcast-series__truncated')) {
        this.episodeList.classList.remove('podcast-series__truncated');
        this.episodeToggle.querySelector('a').textContent = 'View Less';
      } else {
        this.episodeList.classList.add('podcast-series__truncated');
        this.episodeToggle.querySelector('a').textContent = 'View All Podcasts';
      }
    });
  }

  listenElement() {
    this.seasonContainer.addEventListener('touchend', () => {
      const prevScrollLeft = this.seasonTabs.scrollLeft;
      let styleString = '';

      if (this.seasonTabs.scrollLeft < 10) {
        styleString += '--fade-begin: 0%;';
      } else {
        styleString += '--fade-begin: 25%;';
      }

      this.seasonTabs.scrollLeft += 1;
      if (prevScrollLeft === this.seasonTabs.scrollLeft) {
        styleString += '--fade-end: 100%;';
      } else {
        styleString += '--fade-end: 75%;';
      }

      this.seasonContainer.style = styleString;
    });
  }
}

document.querySelectorAll('.podcast-series').forEach(seriesContainer => {
  new PodcastSeries(seriesContainer);
});
