const toggleComponentByViewport = (condition, viewportCondition, store) => {
  const action = ({ instance }) => {
    if (viewportCondition) {
      instance.mount();
    } else {
      instance.unmount();
    }
  };

  store.filter(condition).forEach(action);
};

export default toggleComponentByViewport;
