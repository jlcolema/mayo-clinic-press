import getRelativeTime from '../utils/relative-time';

const API_ENDPOINT = 'https://www.fakerestapi.com/datasets/api/v1/amazon-echo-reviews.json';

class CustomerTestimonials {
  constructor(element) {
    this.element = element;
    this.config();
    this.mount();
  }

  config() {
    this.endpoint = API_ENDPOINT;
    this.observer = new IntersectionObserver(
      this.loadTestimonials.bind(this),
      { rootMargin: '600px', threshold: 0.1 }
   );
    this.items = [];
  }

  mount() {
    this.observer.observe(this.element);
  }

  unmount() {
    this.element.innerHTML = '';
    this.observer.unobserve(this.element);
  }

  async loadTestimonials([entry]) {
    if (!entry.isIntersecting) return;
    this.observer.unobserve(this.element);

    const response = await fetch(this.endpoint);
    if (!response.ok) return; // Exit early on error

    const { data } = await response.json();
    this.items = data;
    this.items.length = 3; // We only want 3 reviews

    this.element.innerHTML = this.buildTestimonialMarkup();
  }

  buildTestimonialMarkup() {
    return this.items.map(item => {
      const { reviewed_at: date, review_text: body, profile_name: name } = item;
      const formattedDate = getRelativeTime(new Date(date));

      return `
        <div class="customer-testimonials__item">
          <p class="customer-testimonials__content">${body}</p>
          <div class="customer-testimonials__info">
            <img src="https://fakeimg.pl/40x40"
              srcset="https://fakeimg.pl/80x80 2x"
              width="40" height="40" alt=""
              loading="lazy">
            <span class="customer-testimonials__name">${name}</span>
            <span class="customer-testimonials__date">${formattedDate}</span>
          </div>
        </div>`;
    }).join('');
  }
}

export default CustomerTestimonials;
