export const setAttributes = (element, attrs) => {
  Object.entries(attrs).forEach(([key, value]) => element.setAttribute(key, value));
};

export const removeAttributes = (element, attrs) => {
  attrs.forEach(key => element.removeAttribute(key));
};

export const cssSupports = (property, value) => (
  CSS.supports(property, value)
);

export const osHasReducedMotion = () => {
  const matchMedia = window.matchMedia('(prefers-reduced-motion: reduce)');
  return matchMedia.matches;
};

export const throttle = (fn, wait = 300) => {
  let lastTime = 0;

  return () => {
    const now = Date.now();
    if (now - lastTime >= wait) {
      fn();
      lastTime = now;
    }
  };
};

export const debounce = (fn, wait = 300) => {
  let timer;

  return (...args) => {
    window.clearTimeout(timer);
    timer = window.setTimeout(() => fn.apply(this, args), wait);
  };
};

export const getFocusableElements = element => (
  element.querySelectorAll('summary, a[href], button:enabled, [tabindex]:not([tabindex^="-"]), [draggable], area, input:not([type="hidden"]):enabled, select:enabled, textarea:enabled, object, iframe')
);

export const isVisible = element => (
  element.offsetWidth || element.offsetHeight || element.getClientRects().length
);

export const wrapElement = (element, wrapper) => {
  while (element.lastChild) {
    wrapper.appendChild(element.firstChild);
  }
  element.appendChild(wrapper);
};

export const unwrapElement = wrapper => {
  wrapper.outerHTML = wrapper.innerHTML;
};

export const createElement = (tag = 'div', attributes = {}, children = []) => {
  const element = document.createElement(tag);
  const {
    class: className,
    id,
    on,
    ...rest
  } = attributes;

  if (className) {
    element.className = className;
  }

  if (id) {
    element.id = id;
  }

  if (on) {
    Object.entries(on).forEach(([name, fn]) => {
      element.addEventListener(name, fn);
    });
  }

  Object.entries(rest).forEach(([key, value]) => {
    element.setAttribute(key, value);
  });

  children.forEach(child => {
    if (typeof child === 'string') {
      child = document.createTextNode(child);
    }
    element.append(child);
  });

  return element;
};

export const asyncWrap = async (fn, ...args) => {
  try {
    return [null, await fn(...args)];
  } catch (error) {
    return [error, null];
  }
};

export const getCookie = name => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
};

export const setCookie = (name, value, days) => {
  const date = new Date();
  date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
  const expires = `; expires=${date.toUTCString()}`;
  document.cookie = `${name}=${value}${expires}; path=/`;
};
