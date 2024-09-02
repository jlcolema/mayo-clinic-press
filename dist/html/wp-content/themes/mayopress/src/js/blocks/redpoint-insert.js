/* eslint-disable no-undef */
function renderPageSmartAssets() {
  fetchRedpointFieldName();
}

if (window.rpiWebClient) {
  if (rpiWebClient.isInitialized) {
    renderPageSmartAssets();
  } else {
    window.addEventListener('rpiWebClientInit', () => {
      renderPageSmartAssets();
    });
  }
} else {
  window.addEventListener('rpiWebClientLoad', () => {
    if (rpiWebClient.isInitialized) {
      renderPageSmartAssets();
    } else {
      window.addEventListener('rpiWebClientInit', () => {
        renderPageSmartAssets();
      });
    }
  });
}
