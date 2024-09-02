import { getFocusableElements } from '.';

/**
 * Reusable focus trap method
 *
 */
function trapFocus(element, opts = {}) {
  let isActive = false;

  const focusableElements = getFocusableElements(element);
  const firstFocusableElement = focusableElements[0];
  const lastFocusableElement = focusableElements[focusableElements.length - 1];

  const defaults = {
    toggleElement: focusableElements[0],
    escape: true,
    onEscape: () => {}
  };
  const options = { ...defaults, ...opts };

  const handleKeyPress = event => {
    if (!isActive || event.ctrlKey || event.metaKey || event.altKey) {
      return;
    }

    if (event.key === 'Escape' && options.escape) {
      options.onEscape();
      options.toggleElement.focus();
    }

    if (event.key === 'Tab') {
      if (event.shiftKey) {
        if (document.activeElement === options.toggleElement) {
          lastFocusableElement.focus();
          event.preventDefault();
        }
      } else if (document.activeElement === lastFocusableElement) {
        options.toggleElement.focus();
        event.preventDefault();
      }
    }
  };

  const activate = () => {
    isActive = true;
    firstFocusableElement.focus();
    document.addEventListener('keydown', handleKeyPress);
  };

  const deactivate = () => {
    isActive = false;
    document.removeEventListener('keydown', handleKeyPress);
  };

  return {
    activate,
    deactivate
  };
}

export default trapFocus;
