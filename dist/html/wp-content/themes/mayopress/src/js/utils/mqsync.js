class MediaQuerySync {
  constructor(viewports) {
    this.viewports = viewports;
    this.activeViewports = {};
    this.subscribers = {};
    this.subType = { on: 'on', off: 'off' };
    this.init();
  }

  init() {
    // Initialize our subscribers with our subscriber types (on and off)
    // This could be extended, but you would probably need subscriber methods added
    Object.keys(this.subType).forEach(key => {
      this.subscribers[key] = {};
    });

    this.validateViewports();
    this.setViewports();
  }

  // Make sure we have an array of objects with a name and media query, else throw error
  validateViewports() {
    const valid = (typeof this.viewports === 'object' && !Array.isArray(this.viewports) && this.viewports !== null
      && Object.values(this.viewports).every(query => typeof query === 'string'));

    if (!valid) throw new Error('Invalid MediaQuerySync configuration');
  }

  // Set our viewports
  setViewports() {
    // Add an event listener to each matchMedia media query and bind our function
    Object.keys(this.viewports).forEach(key => {
      const viewport = this.viewports[key];
      const listener = window.matchMedia(viewport);

      this.viewportSwitch(key, listener);
      listener.addEventListener(
        'change',
        this.viewportSwitch.bind(this, key, listener)
     );
    });
  }

  // Check if matchMedia matches and activate or deactivate actions accordingly
  viewportSwitch(viewport, listener) {
    return listener.matches ? this.activate(viewport) : this.deactivate(viewport);
  }

  // Add items to be notified to subscribers
  subscribe(name, type, fn) {
    this.subscribers[type][name] ??= [];
    this.subscribers[type][name].push({ name, type, action: fn });
  }

  // Search for our named function/action and remove from the subscribers
  unsubscribe(fn) {
    Object.keys(this.subscribers).forEach(type => {
      Object.keys(this.subscribers[type]).forEach(name => {
        this.subscribers[type][name] = this.subscribers[type][name].filter(
          ({ action }) => action !== fn
       );
      });
    });
  }

  // Execute the action(s) assigned to subscribers
  notify(name, type) {
    const subs = this.subscribers[type][name];
    if (subs && Array.isArray(subs)) subs.forEach(sub => sub.action());
  }

  // Add a viewport to the active viewports and run any assigned function
  activate(viewport) {
    if (!this.activeViewports[viewport]) {
      this.activeViewports[viewport] = viewport;
      this.notify(viewport, this.subType.on);
    }
  }

  // Remove a viewport from the active viewports and run any assigned function
  deactivate(viewport) {
    if (this.activeViewports[viewport]) {
      this.activeViewports[viewport] = undefined;
      this.notify(viewport, this.subType.off);
    }
  }

  // Check if the given viewport is currently active
  is(name) {
    return Boolean(this.activeViewports[name]);
  }

  // Execute action when viewport is active and subscribe to changes
  on(name, fn) {
    if (this.is(name)) fn();
    this.change(name, this.subType.on, fn);
  }

  // Execute action when viewport is inactive and subscribe to changes
  // Note this does not unsubscribe; it checks for when we're off a certain viewport size
  off(name, fn) {
    if (!this.is(name)) fn();
    this.change(name, this.subType.off, fn);
  }

  // Subscribe with an action when the named viewport changes (on or off)
  change(name, type, fn) {
    this.subscribe(name, type, fn);
  }

  // Subscribe to viewport changing from active to inactive or vice-versa
  // The difference here is we're watching bi-directionally
  // instead of for one or the other
  watch(name, fn) {
    fn();
    this.change(name, this.subType.on, fn);
    this.change(name, this.subType.off, fn);
  }

  // Get all of our subscribers, or just one breakpoint
  get(name) {
    // Return all if name argument is undefined
    if (!name) return { ...this.subscribers };

    // Find on/off subscribers by name and return
    return Object.entries(this.subscribers).reduce((subs, [type, names]) => {
      if (names[name]) subs[type] = names[name];
      return subs;
    }, {});
  }

  // Get the media query value by key
  getQuery(key) {
    return this.viewports[key];
  }
}

export default MediaQuerySync;
