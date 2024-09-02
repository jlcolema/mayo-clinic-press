class Emitter {
  constructor() {
    this.handlers = {};
  }

  subscribe(event, fn) {
    if (Array.isArray(this.handlers[event])) {
      this.handlers[event] = [...this.handlers[event], fn];
    } else {
      this.handlers[event] = [fn];
    }

    return () => this.off(event, fn);
  }

  unsubscribe(event, fn) {
    this.handlers[event] = this.handlers[event].filter(
      handler => handler !== fn
   );
  }

  notify(event, data) {
    if (!Array.isArray(this.handlers[event])) return;
    this.handlers[event].forEach(handler => handler(data));
  }
}

export default Emitter;
