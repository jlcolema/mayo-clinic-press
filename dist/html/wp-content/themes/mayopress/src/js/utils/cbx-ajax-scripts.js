jQuery(document).ready($ => {
  // Override the delete all functionality
  $('.cbxwpbookmark_deleteall').on('click', () => {
    const catId = document.querySelector('.cbxwpbookmark-mylist-item') ? document.querySelector('.cbxwpbookmark-mylist-item').getAttribute('data-cat-id') : null;
    const data = {
      action: 'delete_bookmarks_by_category',
      security: cbxCustom.nonce,
      cat_id: catId
    };

    setTimeout(() => {
      const confirmOk = document.querySelector('#awn-confirm-ok');

      if (confirmOk) {
        confirmOk.insertAdjacentHTML('afterend', '<button class="awn-btn" id="awn-confirm-ok-2">Yes</button>');
        confirmOk.remove();

        const confirmOkClone = document.querySelector('#awn-confirm-ok-2');
        confirmOkClone.addEventListener('click', () => {
          $.post(cbxCustom.ajaxurl, data, response => {
            if (response && response.success) {
              window.location.reload();
            }
          }).fail((jqXHR, textStatus, errorThrown) => {
            console.error('AJAX Error:', textStatus, errorThrown);
          });
        });
      }
    }, 100);
  });

  // Override the delete bookmark functionality
  $('.cbxwpbookmark-mylist').on('click', '.cbxbookmark-post-delete', e => {
    const target = e.target.closest('.cbxbookmark-delete-btn');
    const catId = document.querySelector('.cbxwpbookmark-mylist-item') ? document.querySelector('.cbxwpbookmark-mylist-item').getAttribute('data-cat-id') : null;
    const bkmId = target.getAttribute('data-bookmark_id');
    const data = {
      action: 'delete_bookmark_by_category_and_id',
      security: cbxCustom.nonce,
      cat_id: catId,
      id: Number(bkmId)
    };

    setTimeout(() => {
      const confirmOk = document.querySelector('#awn-confirm-ok');

      if (confirmOk) {
        confirmOk.insertAdjacentHTML('afterend', '<button class="awn-btn" id="awn-confirm-ok-2">Yes</button>');
        confirmOk.remove();

        const confirmOkClone = document.querySelector('#awn-confirm-ok-2');
        confirmOkClone.addEventListener('click', () => {
          $.post(cbxCustom.ajaxurl, data, response => {
            if (response && response.success) {
              window.location.reload();
            }
          }).fail((jqXHR, textStatus, errorThrown) => {
            console.error('AJAX Error:', textStatus, errorThrown);
          });
        });
      }
    }, 100);
  });
});
