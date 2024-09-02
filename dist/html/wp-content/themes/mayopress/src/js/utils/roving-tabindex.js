/**
 * Switch the tabindex.
 * @param {array} iterables
 * @param {HTMLElement} item
 */
const activate = (iterables, item) => {
  // Set all the target elements to tabindex -1
  iterables.forEach(el => { el.tabIndex = -1; });

  // Make the current target element to "active"
  item.tabIndex = 0;
  item.focus();
};

/**
 * Check for the existence of a valid, focusable previous or next sibling.
 * @param {HTMLElement} item
 * @return {boolean}
 */
const checkItem = item => (
  item && item.hasAttribute('tabindex') && !item.hasAttribute('disabled')
);

/**
 * Figure out if the current element has a next sibling.
 * If so, moving focus to it.
 * @param {array} iterables
 */
const focusNextItem = iterables => {
  const item = document.activeElement;

  // Get next item in index, or wrap around to first item
  const nextItem = iterables[iterables.indexOf(item) + 1] || iterables[0];
  const nextIsVisible = nextItem.offsetWidth > 0 && nextItem.offsetHeight > 0;

  if (checkItem(nextItem) && nextIsVisible) {
    activate(iterables, nextItem);
  }
};

/**
 * Figure out if the current element has a previous sibling.
 * If so, move focus to it.
 * @param {array} iterables
 */
const focusPreviousItem = iterables => {
  const item = document.activeElement;

  // Get previous item in index, or wrap around to last item
  const prevItem = iterables[iterables.indexOf(item) - 1] || iterables[iterables.length - 1];
  const prevIsVisible = prevItem.offsetWidth > 0 && prevItem.offsetHeight > 0;

  if (checkItem(prevItem) && prevIsVisible) {
    activate(iterables, prevItem);
  }
};

/**
 * Enable the roving tabindex and set the control keys
 * @param {Node} parent The element node
 * @param {String} target The type of child elements to be selectable
 * @param {String} prevKey The event.key name to select previous item
 * @param {String} nextKey The event.key name to select next item
 */

const rovingTabindex = (parent, target, prevKey, nextKey) => {
  /**
   * Auto set the tabindex order to prepare
   * the roving tabindex
   */

  const iterables = [...parent.querySelectorAll(target)];
  iterables.forEach((item, index) => {
    item.setAttribute('tabindex', index === 0 ? '0' : '-1');
  });

  /**
   * Activate the roving tabindex navigation
   */
  parent.addEventListener('keydown', event => {
    if (![prevKey, nextKey].includes(event.key)) return;
    event.preventDefault();

    if (event.key === prevKey) {
      focusPreviousItem(iterables);
    }

    if (event.key === nextKey) {
      focusNextItem(iterables);
    }
  }, true);
};

export default rovingTabindex;
