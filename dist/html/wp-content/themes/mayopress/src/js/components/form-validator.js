class FormValidator {
  constructor(element) {
    this.form = element;
    this.config();
    this.mount();
  }

  config() {
    this.options = JSON.parse(this.form.getAttribute('data-options') || '{}');
    this.fields = this.form.querySelectorAll('input:not([type="submit"]), textarea, select');
    this.errorFields = [];
    this.errorFieldListeners = [];
    this.inputErrorClass = 'form-control--error';
    this.inputWrapperClass = 'js-form-validate__input-wrapper';
    this.inputWrapperErrorClass = 'form-validate__input-wrapper--error';
    this.customValidate = this.options.customValidate || {};
    this.validEvent = new CustomEvent('theme/form-validated');
    // Bind event and save so it can be referenced
    this.validate = this.validate.bind(this);
  }

  mount() {
    // Unset default browser form validation
    this.form.noValidate = true;

    // Listen for form submit event
    this.bindEvents();
  }

  unmount() {
    this.form.noValidate = false;
    this.unbindEvents();
  }

  bindEvents() {
    this.form.addEventListener('submit', this.validate);
  }

  unbindEvents() {
    this.form.removeEventListener('submit', this.validate);
  }

  validate(event) {
    event.preventDefault();

    // Reset inputs with errors
    this.errorFields = [];

    // Remove change/input events from fields with error
    this.resetEventListeners();

    // Loop through fields and push to errorFields if there are errors
    this.fields.forEach(field => this.validateField(field));

    if (!this.errorFields.length) {
      // Dispatch a custom event for other components to listen to
      this.form.dispatchEvent(this.validEvent);
      return;
    }

    // Show errors if any were found
    this.errorFields.forEach(errorField => this.showError(errorField));

    // Move focus to first field with error
    this.errorFields[0].focus();
  }

  validateField(field) {
    // Check for required radio/checkbox groups
    // No constraint validation API for these
    let validGroup;
    if (['radio', 'checkbox'].includes(field.type) && field.required) {
      const parent = field.closest('fieldset') || field.parentNode.parentNode;
      validGroup = Boolean(parent.querySelector(`[type="${field.type}"][name="${field.name}"]:checked`));
    }

    if (!field.checkValidity() || validGroup === false) {
      this.errorFields.push(field);
      return;
    }

    // Check for custom validation
    const customValidate = field.getAttribute('data-validate');
    if (customValidate && this.customValidate[customValidate]) {
      this.customValidate[customValidate](field, result => {
        if (!result) this.errorFields.push(field);
      });
    }
  }

  showError(field) {
    // Add error classes
    this.toggleErrorClasses(field, true);

    // Add event listener
    const index = this.errorFieldListeners.length;

    this.errorFieldListeners[index] = () => {
      this.toggleErrorClasses(field, false);
      field.removeEventListener('change', this.errorFieldListeners[index]);
      field.removeEventListener('input', this.errorFieldListeners[index]);
    };

    field.addEventListener('change', this.errorFieldListeners[index]);
    field.addEventListener('input', this.errorFieldListeners[index]);
  }

  toggleErrorClasses(field, bool) {
    field.classList.toggle(this.inputErrorClass, bool);

    if (this.inputWrapperErrorClass) {
      const wrapper = field.closest(`.${this.inputWrapperClass}`);
      if (wrapper) {
        wrapper.classList.toggle(this.inputWrapperErrorClass, bool);
      }
    }
  }

  resetEventListeners() {
    this.errorFields.forEach((field, index) => {
      this.toggleErrorClasses(this, field, false);
      field.removeEventListener('change', this.errorFieldListeners[index]);
      field.removeEventListener('input', this.errorFieldListeners[index]);
    });

    this.errorFields = [];
    this.errorFieldListeners = [];
  }
}

export default FormValidator;
