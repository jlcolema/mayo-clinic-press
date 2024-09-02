/* eslint-disable no-undef */
function renderHomepageRpi() {
  rpiWebClient.renderContextDecisions(['rtl_MCPHomepage']);
}

if (window.rpiWebClient) {
  if (rpiWebClient.isInitialized) {
    renderHomepageRpi();
  } else {
    window.addEventListener('rpiWebClientInit', () => {
      renderHomepageRpi();
    });
  }
} else {
  window.addEventListener('rpiWebClientLoad', () => {
    if (rpiWebClient.isInitialized) {
      renderHomepageRpi();
    } else {
      window.addEventListener('rpiWebClientInit', () => {
        renderHomepageRpi();
      });
    }
  });
}
