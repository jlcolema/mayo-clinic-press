const buttonFunction = () => {
  document.addEventListener('DOMContentLoaded', () => {
    /* MutationObserver Setup */
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        if (mutation.type === 'childList') {
          const noCatFound = document.querySelector('.cbxwpbkmarklist-nocatfound span');
          const list = document.querySelector('.cbxwpbookmark-list-generic');
          const listItems = list.querySelectorAll('.cbxlbjs-item');

          if (noCatFound) {
            observer.disconnect();
            noCatFound.textContent = 'Create your first Collection to start Saving Content';
          } else if (listItems.length) {
            observer.disconnect();
            // sort alphabetically
            const items = Array.from(listItems);
            items.sort((a, b) => {
              const nameA = a.querySelector('.cbxlbjs-item-name').textContent.toUpperCase(); // convert to uppercase for case-insensitive comparison
              const nameB = b.querySelector('.cbxlbjs-item-name').textContent.toUpperCase();

              return nameA.localeCompare(nameB);
            });

            items.forEach(item => list.appendChild(item));
          }
        }
      });
    });
    const observerConfig = { childList: true, subtree: true };

    /* Variables */
    // Wrappers
    const guestWrapper = document.querySelector('.cbxwpbkmarkguestwrap');
    const userWrapper = document.querySelector('.cbxwpbkmarklistwrap');

    // Sections
    const guestMessage = document.querySelector('.cbxwpbkmarkguest-message');
    const userHeader = document.querySelector('.addto-head');
    const userAddForm = document.querySelector('.cbxwpbkmark_cat_add_form');
    const userToolbar = document.querySelector('.cbxwpbkmark-toolbar');

    /* Function to Check and Add Class to Search Bar */
    function checkAndAddClassToSearchBar() {
      const userBookList = document.querySelector('.cbxwpbkmark_cat_book_list');
      if (userBookList && window.getComputedStyle(userBookList).display === 'block') {
        const listItems = userBookList.querySelectorAll('li');
        const searchBarWrapper = document.querySelector('.cbxlbjs-searchbar-wrapper');

        if (listItems.length >= 5 && searchBarWrapper) {
          searchBarWrapper.classList.add('cbxlbjs-searchbar-wrapper--is-visible');
        } else if (searchBarWrapper) {
          searchBarWrapper.classList.remove('cbxlbjs-searchbar-wrapper--is-visible');
        }
      }
    }

    // Initial check and setup observer
    if (document.body.classList.contains('single-post') && document.body.classList.contains('cbxwpbookmark-default')) {
      checkAndAddClassToSearchBar();

      const userBookListObserver = new MutationObserver(checkAndAddClassToSearchBar);
      const userBookList = document.querySelector('.cbxwpbkmark_cat_book_list');
      if (userBookList) {
        userBookListObserver.observe(userBookList, { childList: true, subtree: true });
      }

      // Fallback using setInterval
      setInterval(checkAndAddClassToSearchBar, 1000); // check every 1 second
    }

    /* Function to Add/Remove Class from saveCollection Based on userAddForm Display */
    function updateSaveCollectionVisibility() {
      const saveCollection = document.querySelector('.cbxwpbkmark-field-create-submit');
      if (userAddForm && saveCollection) {
        if (window.getComputedStyle(userAddForm).display === 'block') {
          saveCollection.classList.add('cbxwpbkmark-field-create-submit--is-visible');
        } else {
          saveCollection.classList.remove('cbxwpbkmark-field-create-submit--is-visible');
        }
      }
    }

    /* Set Up a New Observer for userAddForm */
    const userAddFormObserver = new MutationObserver(updateSaveCollectionVisibility);

    if (userAddForm) {
      userAddFormObserver.observe(userAddForm, { attributes: true, attributeFilter: ['style'] });
      updateSaveCollectionVisibility(); // Initial check
    }

    // Clone the "Close" button and move it to the toolbar
    function cloneAndMoveCloseButton() {
      const closeElement = document.querySelector('.addto-head .cbxwpbkmarktrig_close');
      const toolbarElement = document.querySelector('.cbxwpbkmark-toolbar');

      if (closeElement && toolbarElement) {
        const clonedElement = closeElement.cloneNode(true);
        clonedElement.classList.add('btn', 'btn--primary');
        clonedElement.textContent = 'Done';
        toolbarElement.insertBefore(clonedElement, toolbarElement.firstChild);
      }
    }

    // Function to set up a MutationObserver
    function setUpObserver() {
      const addToHead = document.querySelector('.addto-head');
      const toolbarElement = document.querySelector('.cbxwpbkmark-toolbar');

      if (addToHead && toolbarElement) {
        const closeButtonObserver = new MutationObserver(mutations => {
          mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
              if (node.classList && node.classList.contains('cbxwpbkmarktrig_close')) {
                cloneAndMoveCloseButton();
              }
            });
          });
        });

        closeButtonObserver.observe(addToHead, { childList: true, subtree: true });
      }
    }

    // Execute immediately and also set up observer
    cloneAndMoveCloseButton();
    setUpObserver();

    /* When Signed Out */
    if (guestWrapper) {
      if (guestMessage) {
        const bookmarkTitle = guestWrapper.querySelector('.cbxwpbookmark-title');

        if (bookmarkTitle) {
          // Change the text within `.cbxwpbookmark-title`
          bookmarkTitle.textContent = 'Register now to save this content to your library.';

          // Create a "Register" button
          const registerLink = document.createElement('a');
          registerLink.className = 'btn btn--primary';
          registerLink.textContent = 'Register';

          // Copy the link used for "Login here" and use it for the "Register" button
          const nextAnchor = bookmarkTitle.nextElementSibling;
          if (nextAnchor && nextAnchor.tagName === 'A') {
            // Replace 'wp-login.php' with 'my-account/' in the href for both links
            const updatedHref = nextAnchor.href.replace('wp-login.php', 'my-account/');
            nextAnchor.href = updatedHref; // Update the original link
            registerLink.href = updatedHref; // Use the updated link for the "Register" button

            // Insert the "Register" button after `bookmarkTitle`
            bookmarkTitle.parentNode.insertBefore(registerLink, bookmarkTitle.nextSibling);

            // Add "Already have an account?" text before the "Login here" link
            nextAnchor.textContent = 'Login here';
            const paragraph = document.createElement('p');
            paragraph.innerHTML = 'Already have an account? ';
            nextAnchor.parentNode.insertBefore(paragraph, nextAnchor);
            paragraph.appendChild(nextAnchor);
          }
        }
      }
    }

    /* When Signed In */
    if (userWrapper) {
      if (userHeader) {
        const headerTitle = userHeader.querySelector('.cbxwpbkmarktrig_label');

        // Change the header to say "Choose Collection to Save Content"
        if (headerTitle) {
          headerTitle.textContent = 'Choose Collection to Save Content';
        }
      }

      /* Additional MutationObserver for User Header */
      const headerObserver = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
          if (mutation.type === 'childList' || mutation.type === 'characterData') {
            const headerTitle = userHeader.querySelector('.cbxwpbkmarktrig_label');
            if (headerTitle && headerTitle.textContent !== 'Choose Collection to Save Content') {
              headerTitle.textContent = 'Choose Collection to Save Content';
            }
          }
        });
      });

      // Start observing the userHeader for text changes
      if (userHeader) {
        headerObserver.observe(userHeader, { childList: true, characterData: true, subtree: true });
      }

      // Collection Toolbar
      if (userToolbar) {
        const addNewCollection = userToolbar.querySelector('.cbxwpbkmark-toolbar-newcat');
        const editCollections = userToolbar.querySelector('.cbxwpbkmark-toolbar-editcat');

        if (addNewCollection) {
          // Change the text to "Add New Collection"
          addNewCollection.textContent = 'Add New Collection';

          // Move `addNewCollection` to be after `editCollections`
          if (editCollections) {
            userToolbar.insertBefore(addNewCollection, editCollections.nextSibling);
          }

          // Event listener to ensure the header text remains consistent
          addNewCollection.addEventListener('click', () => {
            const headerTitle = userHeader.querySelector('.cbxwpbkmarktrig_label');
            if (headerTitle) {
              headerTitle.textContent = 'Choose Collection to Save Content';
            }

            const target = document.querySelector('.cbxwpbkmark-form-note');
            const bkmarkNoteObserver = new MutationObserver(() => {
              if (target.classList.contains('cbxwpbkmark-form-note-success')) {
                bkmarkNoteObserver.disconnect();
                const backToList = document.querySelector('.cbxwpbkmark-toolbar-listcat');
                if (backToList) backToList.click(); // go back to list
              }
            });
            bkmarkNoteObserver.observe(target, { attributeFilter: ['class'] });
          });
        }

        // Create the `saveCollection` element
        const saveCollection = document.createElement('span');
        saveCollection.classList.add('cbxwpbkmark-field-create-submit', 'btn', 'btn--primary');
        saveCollection.title = 'Save Collection';
        saveCollection.textContent = 'Save';

        // Insert `saveCollection` as the first child within `userToolbar`
        userToolbar.insertBefore(saveCollection, userToolbar.firstChild);
      }
    }

    // Observer for the body
    observer.observe(document.body, observerConfig);
  });
};

// Add "Close" button to the Share modal
const setUpShareUrlObserver = () => {
  const bodyElement = document.body;

  if (bodyElement) {
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
          if (node.querySelector && node.querySelector('.shareurl_copy_btn')) {
            const buttonElement = node.querySelector('.shareurl_copy_btn');

            const newSpan = document.createElement('span');
            newSpan.classList.add('close', 'shareurl__close-button');
            newSpan.innerHTML = '<span class="sr-only">Close</span>';

            // Add an event listener to the newSpan
            newSpan.addEventListener('click', () => {
              const popupWrapper = document.getElementById('awn-popup-wrapper');
              if (popupWrapper) {
                popupWrapper.remove(); // Remove the popup wrapper
              }
            });

            buttonElement.parentNode.insertBefore(newSpan, buttonElement.nextSibling);
          }
        });
      });
    });

    observer.observe(bodyElement, { childList: true, subtree: true });
  }
};

const restyleList = () => {
  /* List of Collections */
  // Variables
  const collectionsList = document.querySelector('.cbxbookmark-category-list-wrap');
  const newCollectionButton = document.querySelector('.cbxbookmark-category-list-create');
  const collectionItems = document.querySelectorAll('.cbxbookmark-category-list-item:not(.restyled)');

  if (collectionsList) {
    // Change the text within `newCollectionButton` to "New Collection"
    if (newCollectionButton) {
      newCollectionButton.textContent = 'New Collection';
    }

    if (collectionItems) {
      collectionItems.forEach(item => {
        item.classList.add('restyled');
        // Variables
        const link = item.querySelector('a');
        const quantity = item.querySelector('i');
        const editButton = item.querySelector('.cbxbookmark-edit-btn');
        // Create a new span for displaying the collection's privacy status
        const status = document.createElement('span');
        status.classList.add('cbxbookmark-category-list-item-status');
        const removeButton = item.querySelector('.cbxbookmark-delete-btn');
        // Create a new button for displaying the collection's actions
        const actionsButton = document.createElement('span');
        actionsButton.classList.add('cbxbookmark-category-list-item-actions');
        actionsButton.innerHTML = `
          <span class="sr-only">Actions</span>
          <span class="dots">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
          </span>
        `;

        // Create a new container for the Edit, Status, and Remove items
        const actionOptions = document.createElement('span');
        actionOptions.classList.add('action-options');

        // Create a new container for `actionOptions` and `actionsButton`
        const actionContainer = document.createElement('span');
        actionContainer.classList.add('action-container');

        // Create a new container called `collectionLinkWrapper`
        const collectionLinkWrapper = document.createElement('span');
        collectionLinkWrapper.classList.add('collection-link-wrapper');

        // Add " saved" within `collectionQuantity`
        if (quantity) {
          quantity.textContent = quantity.textContent.replace(')', ' saved)');
        }

        // Add privacy status of "Private" or "Public" within `status`
        // between the `edit` and `remove` buttons
        if (editButton) {
          if (item.dataset.privacy === '0') {
            status.textContent = 'Private';
            status.insertAdjacentHTML(
              'afterbegin',
              `
                <svg class="icon icon--lock" aria-hidden="true" focusable="false">
                  <use xlink:href="#lock"></use>
                </svg>
              `
            );
            status.classList.add('status--private');
          } else {
            status.textContent = 'Public';
            status.insertAdjacentHTML(
              'afterbegin',
              `
                <svg class="icon icon--lock-unlock" aria-hidden="true" focusable="false">
                  <use xlink:href="#lock-unlock"></use>
                </svg>
              `
            );
            status.classList.add('status--public');
          }
          editButton.parentNode.insertBefore(status, editButton.nextSibling);
        }

        // Add `actionsButton` after `removeButton`
        if (!removeButton) return;

        removeButton.parentNode.insertBefore(actionsButton, removeButton.nextSibling);

        // Toggle the visibility of `actionOptions`
        actionsButton.addEventListener('click', () => {
          // Check if this `actionOptions` is already visible
          const isAlreadyVisible = actionOptions.classList.contains('action-options--is-visible');

          // Remove `.action-options--is-visible` from all elements
          document.querySelectorAll('.action-options--is-visible').forEach(el => {
            el.classList.remove('action-options--is-visible');
          });

          // If this `actionOptions` was not already visible, make it visible
          if (!isAlreadyVisible) {
            actionOptions.classList.add('action-options--is-visible');
          }
        });

        // Add `actionOptions` before `actionsButton`,
        // and append `editButton`, `status`, and `removeButton`
        if (actionsButton) {
          actionsButton.parentNode.insertBefore(actionOptions, actionsButton);
          actionOptions.appendChild(editButton);
          actionOptions.appendChild(status);
          actionOptions.appendChild(removeButton);
        }

        // Add `actionContainer` after `actionsButton`,
        // and append `actionsOptions` and `actionsButton`
        if (actionsButton) {
          actionsButton.parentNode.insertBefore(actionContainer, actionsButton.nextSibling);
          actionContainer.appendChild(actionOptions);
          actionContainer.appendChild(actionsButton);
        }

        // Add `collectionLinkWrapper` before `link`,
        // and append `link` and `quantity`
        if (link) {
          link.parentNode.insertBefore(collectionLinkWrapper, link);
          collectionLinkWrapper.appendChild(link);
          collectionLinkWrapper.appendChild(quantity);
        }

        // Watch for selecting of "Update" or "Close" buttons
        // and then close `actionOptions`
        function closeActionOptions(mutations) {
          mutations.forEach(mutation => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
              const { target } = mutation;

              // Check if the target is `.cbxbookmark-mycat-editbox` and has `display: none`
              if (target.classList.contains('cbxbookmark-mycat-editbox') && target.style.display === 'none') {
                // Find the parent list item
                const listItem = target.closest('.cbxbookmark-category-list-item');
                // Find the `.action-options` sibling and remove the class
                const visibleActionOptions = listItem?.querySelector('.action-options.action-options--is-visible');
                if (visibleActionOptions) {
                  visibleActionOptions.classList.remove('action-options--is-visible');
                }
              }
            }
          });
        }

        // Set up observer for `closeActionOptions`
        const observer = new MutationObserver(closeActionOptions);
        observer.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['style'] });

        // Add the text "Edit" within the `editButton`
        if (editButton) {
          editButton.textContent = 'Edit';
        }

        // Add the text "Remove" within the `removeButton`
        if (removeButton) {
          removeButton.textContent = 'Remove';
        }
      });
    }
  }
};

const accountFunction = () => {
  document.addEventListener('DOMContentLoaded', () => {
    const collectionUlList = document.querySelector('ul.cbxbookmark-category-list');
    if (collectionUlList) {
      let collectionListCount = collectionUlList.querySelectorAll('li:not(.cbxbookmark-category-list-item-notfound)').length;

      const collectionListObserver = new MutationObserver(() => {
        const newListCount = collectionUlList.querySelectorAll('li:not(.cbxbookmark-category-list-item-notfound)').length;
        if (newListCount !== collectionListCount) {
          const listCreateForm = document.querySelector('.cbxbookmark-category-list-create-form');
          // hide create form
          if (listCreateForm) listCreateForm.style.display = 'none';
          // restyle list items again
          if (newListCount > collectionListCount) restyleList();

          // change collection count
          collectionListCount = newListCount;
        }
      });
      collectionListObserver.observe(collectionUlList, { childList: true });
    }

    // restyle list items
    restyleList();

    /* Collection */
    // Variables
    const collectionList = document.querySelector('.cbxwpbookmark-mylist-wrap');
    if (collectionList) {
      // Move the "Delete All" and "Share" buttons after the list of posts
      const collectionTitle = collectionList.querySelector('.cbxwpbookmark-title-postlist');

      if (collectionTitle) {
        const deleteAll = collectionTitle.querySelector('.cbxwpbookmark_deleteall');
        const share = collectionTitle.querySelector('.cbxwpbookmark_share');
        const privacySetting = document.querySelector('.cbxwpbookmark-mylist-item') ? document.querySelector('.cbxwpbookmark-mylist-item').dataset.privacy : null;

        if (deleteAll && share) {
          share.insertAdjacentHTML(
            'beforeend',
            `
              <svg class="icon icon--external-link" aria-hidden="true" focusable="false">
                <use xlink:href="#external-link"></use>
              </svg>
            `
          );
          const collectionActions = document.createElement('div');
          collectionActions.classList.add('collection-actions');
          collectionActions.appendChild(share);
          collectionActions.appendChild(deleteAll);
          collectionList.parentNode.insertBefore(collectionActions, collectionList.nextSibling);

          if (privacySetting === '1') {
            share.addEventListener('click', () => {
              const bodyElem = document.querySelector('body');
              bodyElem.classList.add('share');

              setTimeout(() => {
                const popupWrapper = document.querySelector('#awn-popup-wrapper');

                share.after(popupWrapper);
                bodyElem.addEventListener('click', e => {
                  const { target } = e;
                  if (!target.closest('#awn-popup-wrapper') && popupWrapper) {
                    popupWrapper.remove();
                    if (!document.querySelector('.collection-actions #awn-popup-wrapper')) {
                      setTimeout(() => {
                        bodyElem.classList.remove('share');
                      }, 150);
                    }
                  }
                }, { once: true });
              }, 100);
            });
          } else {
            share.remove();
          }
        }
      }

      // Add new content
      const myListItems = document.querySelectorAll('.cbxwpbookmark-mylist-item');
      if (myListItems.length) {
        myListItems.forEach(async item => {
          if (!item.querySelector('a')) return;

          const url = item.querySelector('a').href;
          // Keep the original delete button
          const deleteButton = item.querySelector('.cbxbookmark-delete-btn').cloneNode(true);

          try {
            const response = await fetch(url);
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const postImage = doc.querySelector('.singular__thumbnail img')?.outerHTML || '';
            const postFormat = doc.querySelector('.singular__topic .post--format')?.textContent || '';
            const postCategory = doc.querySelector('.singular__topic .post--category')?.textContent || '';
            const postLink = item.querySelector('a')?.href;
            const postTitle = item.querySelector('a')?.textContent;
            const postDate = doc.querySelector('.singular__date');
            const postDateTime = postDate?.getAttribute('datetime') || '';
            const postMonthDayYear = postDate?.textContent || '';
            const postAuthor = doc.querySelector('.singular__author')?.textContent || '';

            const newHtml = `
              <div class="cbxwpbookmark-mylist-item-entry">
                <a href="${postLink}">
                  <div class="excerpt__image">
                    <figure class="singular__thumbnail">
                      ${postImage}
                    </figure>
                  </div>
                  <div class="excerpt__description">
                    <div class="term-category">
                      <span class="post--format">${postFormat}</span> |
                      <span class="post--category">${postCategory}</span>
                    </div>
                    <h2 class="excerpt__title">${postTitle}</h2>
                    <time class="excerpt__date" datetime="${postDateTime}">
                      <span class="sr-only">Posted on </span>${postMonthDayYear}
                    </time>
                    <span style="color: var(--color-primary);">•</span>
                    <span class="singular__author">${postAuthor}</span>
                  </div>
                </a>
              </div>
            `;
            item.innerHTML = newHtml;
            // Appending the delete button as the last child
            item.appendChild(deleteButton);
            item.style.display = 'flex';
          } catch (error) {
            item.appendChild(deleteButton);
            item.style.display = 'block'; // Show the item even if there's an error
          }
        });

        setUpShareUrlObserver();
      }
    }
  });
};

export { buttonFunction, accountFunction };
