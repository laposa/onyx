function pdf2webViewer(params) {
  // assets
  var iconPage =
    '<svg fill="none" height="24" stroke-width="1.5" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L14 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 10H18" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 14L18 14" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 18L18 18" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 21.4V2.6C2 2.26863 2.26863 2 2.6 2H18.2515C18.4106 2 18.5632 2.06321 18.6757 2.17574L21.8243 5.32426C21.9368 5.43679 22 5.5894 22 5.74853V21.4C22 21.7314 21.7314 22 21.4 22H2.6C2.26863 22 2 21.7314 2 21.4Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 5.4V2.35355C18 2.15829 18.1583 2 18.3536 2C18.4473 2 18.5372 2.03725 18.6036 2.10355L21.8964 5.39645C21.9628 5.46275 22 5.55268 22 5.64645C22 5.84171 21.8417 6 21.6464 6H18.6C18.2686 6 18 5.73137 18 5.4Z" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 18V14H8V18H6Z" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  var iconHotspot =
    '<svg height="20" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><g fill="none"><path d="m0 0h256v256h-256z"/><g stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"><path d="m140 48h-24"/><path d="m116 208h24"/><path d="m180 48h20a8 8 0 0 1 8 8v20"/><path d="m208 140v-24"/><path d="m48 116v24"/><path d="m76 208h-20a8 8 0 0 1 -8-8v-20"/><path d="m76 48h-20a8 8 0 0 0 -8 8v20"/><path d="m208 180v56"/><path d="m236 208h-56"/></g></g></svg>';

  // init variables
  var currentPage = 1;
  var showingTwoPages;
  var currentHotspot;
  var pages = params.manifest.pages.sort((a, b) => {
    return a.order - b.order;
  });
  var numPages = pages.length;
  var pagesElement;

  if (params.showEditor) {
    if (isMobile()) params.showEditor = false;
    else params.showTwoPages = false;
  }

  // init DOM elements
  params.target.classList.add("pdf2web-wrapper");
  createAllPages();
  createPagination();
  attachKeyboardHandlers();
  attachSwipeHandlers();
  attachHashChangeHandler();
  if (params.showEditor) initEditor();

  function firstImageLoaded() {
    params.target.classList.add("loaded");
    var resizeTimeout;
    window.addEventListener("resize", function () {
      // debounce the resize
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        updateLayout();
      }, 200);
    });
    updateAspectRatio();
  }

  function updateAspectRatio() {
    var firstImage = params.target.querySelector(".pdf2web-page-1 > img");
    var width = firstImage.naturalWidth;
    var height = firstImage.naturalHeight;
    if (params.showTwoPages && !isMobile()) width = width * 2;
    pagesElement.style.aspectRatio = width + "/" + height;
  }

  function isMobile() {
    return params.target.clientWidth <= params.mobileBreakpoint;
  }

  // switch between 1-page and 2-page layout depending on contaner size
  function updateLayout() {
    if (params.showTwoPages) {
      if (isMobile()) {
        showingTwoPages = false;
        pagesElement.classList.remove("pdf2web-pages-show-two");
        goToPage(currentPage);
      } else {
        showingTwoPages = true;
        pagesElement.classList.add("pdf2web-pages-show-two");
        goToPage(currentPage);
      }
    }
    updateAspectRatio();
  }

  function createAllPages() {
    pagesElement = document.createElement("div");
    pagesElement.className = "pdf2web-pages";
    if (params.showTwoPages && !isMobile()) {
      pagesElement.className += " pdf2web-pages-show-two pdf2web-cover-page-visible";
      if (pages.length == 2) pagesElement.className += " pdf2web-two-page-book";
      showingTwoPages = true;
    }
    pages.forEach(function (page, index) {
      createSinglePage(pagesElement, page, index + 1);
    });
    params.target.prepend(pagesElement);
  }

  function createSinglePage(target, page, pageNum) {
    var div = document.createElement("div");
    var odd = " odd";
    if (pageNum % 2 == 0) odd = " even";
    div.className = "pdf2web-page pdf2web-page-" + pageNum + odd;
    div.dataset.page = pageNum;
    div.style.zIndex = 1000 + numPages - pageNum;
    var img = document.createElement("img");
    img.src = params.imagesBaseUrl + page.filename;
    img.alt = "Page " + pageNum;
    if (pageNum == 1) {
      img.addEventListener("load", firstImageLoaded);
    }
    createHotspots(div, pageNum - 1, page.hotspots);
    div.appendChild(img);
    target.appendChild(div);
  }

  function createHotspots(element, pageIndex, hotspots) {
    if (!hotspots || !hotspots.length) return;
    hotspots.forEach(function (hotspot, index) {
      insertHotspotElement(element, hotspot, pageIndex, index);
    });
  }

  function insertHotspotElement(element, hotspot, pageIndex, hotspotIndex) {
    var a = document.createElement("a");
    a.className = "pdf2web-hotspot";
    a.style.left = hotspot.left + "%";
    a.style.top = hotspot.top + "%";
    a.style.width = hotspot.width + "%";
    a.style.height = hotspot.height + "%";
    a.setAttribute("href", hotspot.url);
    if (!hotspot.url.startsWith('#') && !isSameHostname(hotspot.url)) a.setAttribute("target", "_blank");
    a.dataset.page = pageIndex;
    a.dataset.hotspot = hotspotIndex;
    a.addEventListener("click", function (e) {
      if (params.showEditor) e.preventDefault();
    });
    if (params.showEditor) a.addEventListener("mousedown", handleHotspotMouseDown);
    if (hotspot.title) {
      a.setAttribute("data-title", hotspot.title);
      a.setAttribute("aria-label", hotspot.title);
    }
    element.appendChild(a);
    return a;
  }

  function attachSwipeHandlers() {
    var startX = 0;
    var isSwiping = false;
    var pageFlipped = false;
    var rotate = 0;
    var item;
    var prev;

    function isZoomed() {
      var windowWidth = window.innerWidth;
      var documentWidth = document.documentElement.clientWidth;
      return windowWidth < documentWidth;
    }

    function getFlipAmount(currentX) {
      var containerRect = params.target.getBoundingClientRect();
      var mult = showingTwoPages ? 4 : 2;
      var flipAmount = ((currentX - startX) / containerRect.width) * mult;
      flipAmount = Math.min(flipAmount, 1);
      flipAmount = Math.max(flipAmount, -1);
      return flipAmount;
    }

    function touchStart(event) {
      if (params.showEditor) {
        handleHotspotCreation(event);
        return;
      }
      if (event.type === "touchstart") {
        if (event.touches.length > 1 || isZoomed()) return;
        startX = event.touches[0].clientX;
        if (!event.target.classList.contains("pdf2web-hotspot")) event.preventDefault();
      } else if (event.type === "mousedown") {
        if (isZoomed()) return;
        startX = event.clientX;
        event.preventDefault();
      }

      isSwiping = true;
      var itemIndex = currentPage - 1;
      if (showingTwoPages && currentPage > 1) itemIndex = currentPage;
      var items = pagesElement.querySelectorAll(".pdf2web-page");
      if (itemIndex >= items.length - 1) {
        item = null;
        prev = items[itemIndex - 1];
      } else {
        item = items[itemIndex];
        prev = item.previousElementSibling;
      }
    }

    function touchMove(event) {
      if (pageFlipped) event.preventDefault();

      var clientX;
      if (event.type === "touchmove") {
        if (!isSwiping || event.touches.length > 1 || isZoomed()) return;
        clientX = event.touches[0].clientX;
      } else if (event.type === "mousemove") {
        if (!isSwiping || isZoomed()) return;
        clientX = event.clientX;
      }

      event.preventDefault();

      var flipAmount = getFlipAmount(clientX);
      if (flipAmount < 0) {
        rotate = flipAmount * 90;
        if (item) {
          item.style.transform = "rotateY(" + rotate + "deg)";
          item.style.transition = "all 0.1s ease";
          if (rotate < -70) {
            isSwiping = false;
            pageFlipped = true;
            resetSwipingTransition();
            goToPage(getNextPage());
          }
        }
      }
      if (flipAmount > 0) {
        if (item) {
          item.style.transform = "rotateY(0deg)";
          item.style.transition = "all 0.1s ease";
        }
        if (prev) {
          if (showingTwoPages) {
            rotate = flipAmount * 90;
          } else {
            rotate = -90 + flipAmount * 90;
          }
          prev.style.transform = "rotateY(" + rotate + "deg)";
          prev.style.transition = "all 0.1s ease";
          if (rotate >= 70 || (rotate < 0 && rotate > -45)) {
            isSwiping = false;
            pageFlipped = true;
            resetSwipingTransition();
            goToPage(getPreviousPage());
          }
        }
      }
    }

    function resetSwipingTransition() {
      if (item) {
        item.style.transform = "";
        item.style.transition = "";
      }
      if (prev) {
        prev.style.transform = "";
        prev.style.transition = "";
      }
    }

    function touchEnd(event) {
      pageFlipped = false;
      resetSwipingTransition();
      isSwiping = false;
    }

    pagesElement.addEventListener("touchstart", touchStart);
    pagesElement.addEventListener("touchmove", touchMove);
    pagesElement.addEventListener("touchend", touchEnd);
    pagesElement.addEventListener("mousedown", touchStart);
    pagesElement.addEventListener("mousemove", touchMove);
    pagesElement.addEventListener("mouseup", touchEnd);
    pagesElement.addEventListener("mouseleave", touchEnd);
  }

  function createPagination() {
    var div = document.createElement("div");
    div.className = "pdf2web-pagination-container";
    div.innerHTML = `
      <ul class="pdf2web-pagination">
        <li class="pdf2web-pagination-first"><a href="#" aria-label="Go to first page">First</a></li>
        <li class="pdf2web-pagination-prev"><a href="#" aria-label="Go to previous page">Previous</a></li>
        <li class="pdf2web-pagination-nums"></li>
        <li class="pdf2web-pagination-next"><a href="#" aria-label="Go to next page">Next</a></li>
        <li class="pdf2web-pagination-last"><a href="#" aria-label="Go to last page">Last</a></li>
      </ul>
    `;
    params.target.appendChild(div);
    updatePagination();
    params.target.querySelector(".pdf2web-pagination-first").addEventListener("click", function (e) {
      e.preventDefault();
      animateToPage(1);
    });
    params.target.querySelector(".pdf2web-pagination-prev").addEventListener("click", function (e) {
      e.preventDefault();
      goToPage(getPreviousPage());
    });
    params.target.querySelector(".pdf2web-pagination-next").addEventListener("click", function (e) {
      e.preventDefault();
      goToPage(getNextPage());
    });
    params.target.querySelector(".pdf2web-pagination-last").addEventListener("click", function (e) {
      e.preventDefault();
      animateToPage(numPages);
    });
  }

  function animateToPage(targetPage) {
    if (currentPage > targetPage) {
      var prev = getPreviousPage();
      if (next == currentPage) return;
      setTimeout(function () {
        goToPage(prev);
        animateToPage(targetPage);
      }, 100);
    } else if (currentPage < targetPage) {
      var next = getNextPage();
      if (next == currentPage) return;
      setTimeout(function () {
        goToPage(next);
        animateToPage(targetPage);
      }, 100);
    }
  }

  function getPreviousPage() {
    var prevPage = currentPage - 1;
    if (showingTwoPages) prevPage = currentPage - 2;
    if (prevPage <= 1) prevPage = 1;
    return prevPage;
  }

  function getNextPage() {
    var nextPage = currentPage + 1;
    if (showingTwoPages) {
      if (currentPage == 1) nextPage = currentPage + 1;
      else nextPage = currentPage + 2;
    }
    if (nextPage > numPages) nextPage = currentPage;
    return nextPage;
  }

  function getLastPage() {
    var lastPage = numPages;
    if (showingTwoPages) {
      var lastPageEven = numPages % 2 == 0;
      if (lastPageEven) lastPage = numPages;
      else lastPage = numPages - 1;
    }
    return lastPage;
  }

  function goToPage(page) {
    currentPage = page;
    if (currentPage == 1) pagesElement.classList.add("pdf2web-first");
    else pagesElement.classList.remove("pdf2web-first");
    if (currentPage == numPages) pagesElement.classList.add("pdf2web-last");
    else pagesElement.classList.remove("pdf2web-last");
    if (showingTwoPages) {
      var isPageEven = page % 2 !== 0;
      if (isPageEven && page > 1) page--;
    }
    params.target.querySelectorAll(".pdf2web-page").forEach(function (page, index) {
      page.classList.remove("flipped");
      page.classList.remove("open");
      page.classList.remove("opposite");
      if (index + 1 < currentPage) {
        page.classList.add("flipped");
      } else {
        if (index + 1 == currentPage) {
          if (currentPage > 1) page.classList.add("open");
          else page.classList.add("opposite");
        } else if (index == currentPage) {
          page.classList.add("opposite");
        }
      }
    });
    window.location.hash = 'page' + page;
    updatePagination();
    centerCoverPage();
  }

  function centerCoverPage() {
    if (currentPage == 1) {
      params.target.querySelector(".pdf2web-pages").classList.add("pdf2web-cover-page-visible");
    } else {
      params.target.querySelector(".pdf2web-pages").classList.remove("pdf2web-cover-page-visible");
    }
  }

  function updatePagination() {
    var num = params.target.querySelector(".pdf2web-pagination-nums");
    if (showingTwoPages && currentPage > 1 && currentPage < numPages) page = currentPage + '-' + (currentPage + 1);
    else page = currentPage;
    num.innerText = page + " / " + numPages;
    if (currentPage == 1) {
      params.target.querySelector(".pdf2web-pagination-first").classList.add("pdf2web-disabled");
      params.target.querySelector(".pdf2web-pagination-prev").classList.add("pdf2web-disabled");
    } else {
      params.target.querySelector(".pdf2web-pagination-first").classList.remove("pdf2web-disabled");
      params.target.querySelector(".pdf2web-pagination-prev").classList.remove("pdf2web-disabled");
    }
    if (currentPage == pages.length) {
      params.target.querySelector(".pdf2web-pagination-last").classList.add("pdf2web-disabled");
      params.target.querySelector(".pdf2web-pagination-next").classList.add("pdf2web-disabled");
    } else {
      params.target.querySelector(".pdf2web-pagination-last").classList.remove("pdf2web-disabled");
      params.target.querySelector(".pdf2web-pagination-next").classList.remove("pdf2web-disabled");
    }
  }

  function attachKeyboardHandlers() {
    document.addEventListener("keydown", function (event) {
      if (currentHotspot) {
        switch (event.key) {
          case "Escape":
            event.preventDefault();
            cancelHotspotEdit(event);
            break;
        }
      } else {
        switch (event.key) {
          case "ArrowLeft":
            event.preventDefault();
            goToPage(getPreviousPage());
            break;

          case "ArrowRight":
            event.preventDefault();
            goToPage(getNextPage());
            break;

          case "Home":
            event.preventDefault();
            animateToPage(1);
            break;

          case "End":
            event.preventDefault();
            animateToPage(numPages);
            break;
        }
      }
    });
  }

  function initEditor() {
    editorElement = document.createElement("div");
    editorElement.className = "pdf2web-editor-form";
    editorElement.innerHTML = `<form>
      <div class="pdf2web-editor-form-list">
        <h2>All Hotspots</h2>
        <ul class="pdf2web-editor-page-list"></ul>
      </div>
      <div class="pdf2web-editor-form-fields">
        <h2>Edit Hotspot</h2>
        <a href="#" class="pdf2web-editor-form-back-arrow">←</a>
        <label><input type="text" name="title" class="pdf2web-input-title" required placeholder="Title" /></label>
        <label><textarea name="url" class="pdf2web-textarea-link" required placeholder="Link"></textarea></label>
        <div class="pdf2web-number-input-wrapper">
          <label>
            <span class="pdf2web-label">X</span>
            <input type="text" name="left" class="pdf2web-input-x" maxlength="7" required readonly />
            <span class="pdf2web-unit">%</span>
          </label>
          <label>
            <span class="pdf2web-label">Y</span>
            <input type="text" name="top" class="pdf2web-input-y" maxlength="7" required readonly />
            <span class="pdf2web-unit">%</span>
          </label>
        </div>
        <div class="pdf2web-number-input-wrapper">
          <label>
            <span class="pdf2web-label">Width</span>
            <input type="text" name="width" class="pdf2web-input-width" maxlength="7" required readonly />
            <span class="pdf2web-unit">%</span>
          </label>
          <label>
            <span class="pdf2web-label">Height</span>
            <input type="text" name="height" class="pdf2web-input-height" maxlength="7" required readonly />
            <span class="pdf2web-unit">%</span>
          </label>
        </div>
      </div>
      <div class="pdf2web-toolbar">
        <button class="pdf2web-button pdf2web-button-add-hotspot">Add Hotspot</button>
        <button class="pdf2web-button pdf2web-button-remove-hotspot">Remove Hotspot</button>
        <button class="pdf2web-button pdf2web-button-save">Save</button>
      </div></form>
    `;
    params.target.classList.add("pdf2web-editor-enabled");
    params.target.prepend(editorElement);
    var back = params.target.querySelector(".pdf2web-editor-form-back-arrow");
    back.addEventListener("click", cancelHotspotEdit);
    updateHotspotList();
    attachEventsToFormElements();
  }

  function updateHotspotList() {
    var list = params.target.querySelector(".pdf2web-editor-page-list");
    var listHtml = "";
    pages.forEach(function (page, index) {
      listHtml += `<li class="pdf2web-li-page" data-index="${index}">${iconPage} <a href="#">Page ${index + 1}</a>`;
      if (page.hotspots && page.hotspots.length > 0) {
        listHtml += `<ul>`;
        page.hotspots.forEach(function (hotspot, index) {
          listHtml += `<li class="pdf2web-li-hotspot"><a href="#" data-index="${index}">${iconHotspot} ${hotspot.title}</a></li>`;
        });
        listHtml += `</ul>`;
      } else {
        listHtml += `<p class="pdf2web-page-empty">
          Drag the mouse cursor over the page to create a new clickable hotspot
          area.
        </p>`;
      }
      listHtml += "</li>";
    });
    list.innerHTML = listHtml;
    attachEventsToEditorElements();
  }

  function attachEventsToFormElements() {
    var inputs = params.target.querySelectorAll(
      ".pdf2web-editor-form-fields input, .pdf2web-editor-form-fields textarea"
    );
    inputs.forEach(function (el) {
      el.addEventListener("change", function () {
        updateHotspotAttribute(currentHotspot, el.getAttribute("name"), el.value);
      });
    });

    var saveForm = params.target.querySelector("form");
    saveForm.addEventListener("submit", saveEditorState);

    var addHotspotButton = params.target.querySelector(".pdf2web-button-add-hotspot");
    addHotspotButton.addEventListener("click", addNewHotspot);

    var removeHotspotButton = params.target.querySelector(".pdf2web-button-remove-hotspot");
    removeHotspotButton.addEventListener("click", removeCurrentHotspot);
  }

  function saveEditorState(e) {
    // TODO: add validation
    cancelHotspotEdit(e);
    params.saveHandler(params.manifest);
  }

  function addNewHotspot(e) {
    cancelHotspotEdit(e);
    var pageIndex = currentPage - 1;
    var hotspotIndex = addHotspotToPage(pageIndex, "New Hotspot", "https://", 30, 40, 40, 20);
    currentHotspot = null;
    editHotspot(pageIndex, hotspotIndex);
    updateHotspotList();
  }

  function removeCurrentHotspot(e) {
    var hotspotElement = currentHotspot;
    cancelHotspotEdit(e);
    var pageIndex = hotspotElement.dataset.page * 1;
    var hotspotIndex = hotspotElement.dataset.hotspot * 1;
    pages[pageIndex].hotspots.splice(hotspotIndex, 1);
    hotspotElement.remove();
    updateHotspotList();
  }

  function updateHotspotAttribute(hotspotElement, attributeName, value) {
    var pageIndex = hotspotElement.dataset.page * 1;
    var hotspotIndex = hotspotElement.dataset.hotspot * 1;
    pages[pageIndex].hotspots[hotspotIndex][attributeName] = value;
    refreshHotspotElement(hotspotElement, pages[pageIndex].hotspots[hotspotIndex]);
    updateHotspotList();
  }

  function refreshHotspotElement(hotspotElement, hotspot) {
    var a = hotspotElement;
    a.style.left = hotspot.left + "%";
    a.style.top = hotspot.top + "%";
    a.style.width = hotspot.width + "%";
    a.style.height = hotspot.height + "%";
    a.setAttribute("href", hotspot.url);
    if (hotspot.title) {
      a.setAttribute("data-title", hotspot.title);
      a.setAttribute("aria-label", hotspot.title);
    }
  }

  function attachEventsToEditorElements() {
    var back = params.target.querySelector(".pdf2web-editor-form-back-arrow");
    back.addEventListener("click", cancelHotspotEdit);

    var pageLinks = params.target.querySelectorAll("li.pdf2web-li-page > a");
    pageLinks.forEach(function (pageLink) {
      pageLink.addEventListener("click", function (e) {
        e.preventDefault();
        animateToPage(e.target.parentNode.dataset.index * 1 + 1);
      });
    });

    var hotspotLinks = params.target.querySelectorAll("li.pdf2web-li-hotspot > a");
    hotspotLinks.forEach(function (hotSpotLink) {
      hotSpotLink.addEventListener("click", function (e) {
        e.preventDefault();
        var pageIndex = e.target.closest("li.pdf2web-li-page").dataset.index * 1;
        var hotspotIndex = e.target.dataset.index * 1;
        editHotspot(pageIndex, hotspotIndex);
      });
    });
  }

  function editHotspot(pageIndex, hotspotIndex) {
    var form = params.target.querySelector(".pdf2web-editor-form");
    form.classList.add("pdf2web-edit-hotspot");
    updateHotspotEditForm(pageIndex, hotspotIndex);
    makeHotspotResizable(pageIndex, hotspotIndex);

    setTimeout(function () {
      form.querySelector(".pdf2web-input-title").focus();
    }, 600);

    animateToPage(pageIndex + 1);
  }

  function handleHotspotMouseDown(e) {
    if (!e.target.classList.contains("pdf2web-hotspot")) return;
    var pageIndex = e.target.dataset.page * 1;
    var hotspotIndex = e.target.dataset.hotspot * 1;
    editHotspot(pageIndex, hotspotIndex);

    var parent = currentHotspot.closest(".pdf2web-page");
    var isDragging = true;
    var offsetX, offsetY;
    var initialWidth, initialHeight;
    if (e.target !== currentHotspot) return;

    const parentRect = parent.getBoundingClientRect();
    offsetX = e.clientX - currentHotspot.getBoundingClientRect().left;
    offsetY = e.clientY - currentHotspot.getBoundingClientRect().top;
    initialWidth = parseFloat(currentHotspot.style.width);
    initialHeight = parseFloat(currentHotspot.style.height);

    document.addEventListener("mousemove", onHotspotMouseMove);
    document.addEventListener("mouseup", onHotspotMouseUp);

    function onHotspotMouseMove(e) {
      if (!isDragging) return;
      const newX = Math.max(
        0,
        Math.min(toPerc(e.clientX - parentRect.left - offsetX, parentRect.width), 100 - initialWidth)
      );
      const newY = Math.max(
        0,
        Math.min(toPerc(e.clientY - parentRect.top - offsetY, parentRect.height), 100 - initialHeight)
      );
      currentHotspot.style.left = `${newX}%`;
      currentHotspot.style.top = `${newY}%`;
      pages[pageIndex].hotspots[hotspotIndex].left = newX.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].top = newY.toFixed(2);
      updateHotspotEditForm(pageIndex, hotspotIndex);
    }

    function onHotspotMouseUp() {
      isDragging = false;
      document.removeEventListener("mousemove", onHotspotMouseMove);
      document.removeEventListener("mouseup", onHotspotMouseUp);
    }
  }

  function makeHotspotResizable(pageIndex, hotspotIndex) {
    var hotspotElements = params.target.querySelectorAll(".pdf2web-hotspot");
    var hotspotElement = Array.from(hotspotElements).find(
      (el) => el.dataset.page == pageIndex && el.dataset.hotspot == hotspotIndex
    );
    if (currentHotspot == hotspotElement) return;
    currentHotspot = hotspotElement;
    hotspotElements.forEach((el) => {
      el.classList.remove("edited");
      el.classList.remove("new");
      el.innerHTML = "";
    });
    currentHotspot.classList.add("edited");
    currentHotspot.draggable = false;
    appendResizeIndicators(currentHotspot);

    var parent = currentHotspot.closest(".pdf2web-page");
    var isResizing = false;
    var currentHandle;
    var initialWidth, initialHeight;
    var initialX, initialY;

    currentHotspot.querySelectorAll(".pdf2web-resize-handle").forEach((handle) => {
      handle.addEventListener("mousedown", (e) => {
        isResizing = true;
        currentHandle = handle;
        e.stopPropagation();
        initialWidth = parseFloat(currentHotspot.style.width);
        initialHeight = parseFloat(currentHotspot.style.height);
        initialX = parseFloat(currentHotspot.style.left) || 0;
        initialY = parseFloat(currentHotspot.style.top) || 0;
        document.addEventListener("mousemove", onResizeMouseMove);
        document.addEventListener("mouseup", onResizingMouseUp);
      });
    });

    function onResizeMouseMove(e) {
      if (!isResizing) return;

      var parentRect = parent.getBoundingClientRect();
      var newX = initialX;
      var newY = initialY;
      var newWidth = initialWidth;
      var newHeight = initialHeight;

      var percX = inRange(toPerc(e.clientX - parentRect.left, parentRect.width), 0, 100);
      var percY = inRange(toPerc(e.clientY - parentRect.top, parentRect.height), 0, 100);
      var c = currentHandle.classList;

      if (c.contains("pdf2web-n") || c.contains("pdf2web-nw") || c.contains("pdf2web-ne")) {
        newHeight = inRange(initialHeight - percY + initialY, 5, 100);
        newY = inRange(initialY + initialHeight - newHeight, 0, 100 - newHeight);
      }
      if (c.contains("pdf2web-s") || c.contains("pdf2web-sw") || c.contains("pdf2web-se")) {
        newHeight = inRange(initialHeight + (percY - initialHeight - initialY), 5, 100);
      }
      if (c.contains("pdf2web-e") || c.contains("pdf2web-ne") || c.contains("pdf2web-se")) {
        newWidth = inRange(initialWidth + (percX - initialWidth - initialX), 5, 100);
      }
      if (c.contains("pdf2web-w") || c.contains("pdf2web-nw") || c.contains("pdf2web-sw")) {
        newWidth = inRange(initialWidth - percX + initialX, 5, 100);
        newX = inRange(initialX + initialWidth - newWidth, 0, 100 - newWidth);
      }

      currentHotspot.style.left = `${newX}%`;
      currentHotspot.style.top = `${newY}%`;
      currentHotspot.style.width = `${newWidth}%`;
      currentHotspot.style.height = `${newHeight}%`;
      pages[pageIndex].hotspots[hotspotIndex].left = newX.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].top = newY.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].width = newWidth.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].height = newHeight.toFixed(2);
      updateHotspotEditForm(pageIndex, hotspotIndex);
    }

    function onResizingMouseUp() {
      isResizing = false;
      document.removeEventListener("mousemove", onResizeMouseMove);
      document.removeEventListener("mouseup", onResizingMouseUp);
    }
  }

  function appendResizeIndicators(hotspotElement) {
    hotspotElement.innerHTML = `
      <div class="pdf2web-resize-handle pdf2web-corner pdf2web-nw"></div>
      <div class="pdf2web-resize-handle pdf2web-corner pdf2web-ne"></div>
      <div class="pdf2web-resize-handle pdf2web-corner pdf2web-se"></div>
      <div class="pdf2web-resize-handle pdf2web-corner pdf2web-sw"></div>
      <div class="pdf2web-resize-handle pdf2web-edge pdf2web-n"></div>
      <div class="pdf2web-resize-handle pdf2web-edge pdf2web-e"></div>
      <div class="pdf2web-resize-handle pdf2web-edge pdf2web-s"></div>
      <div class="pdf2web-resize-handle pdf2web-edge pdf2web-w"></div>
    `;
  }

  function updateHotspotEditForm(pageIndex, hotspotIndex) {
    var hotspot = pages[pageIndex].hotspots[hotspotIndex];
    var form = params.target.querySelector(".pdf2web-editor-form");
    var titleField = form.querySelector(".pdf2web-input-title");
    var linkField = form.querySelector(".pdf2web-textarea-link");
    var xField = form.querySelector(".pdf2web-input-x");
    var yField = form.querySelector(".pdf2web-input-y");
    var widthField = form.querySelector(".pdf2web-input-width");
    var heightField = form.querySelector(".pdf2web-input-height");
    titleField.value = hotspot.title;
    linkField.value = hotspot.url;
    xField.value = hotspot.left;
    yField.value = hotspot.top;
    widthField.value = hotspot.width;
    heightField.value = hotspot.height;
  }

  function cancelHotspotEdit(e) {
    e.preventDefault();
    var form = params.target.querySelector(".pdf2web-editor-form");
    form.classList.remove("pdf2web-edit-hotspot");
    var hotspotElements = params.target.querySelectorAll(".pdf2web-hotspot");
    hotspotElements.forEach((el) => {
      el.classList.remove("edited");
      el.classList.remove("new");
      el.innerHTML = "";
    });
    currentHotspot = null;
  }

  function addHotspotToPage(pageIndex, title, link, x, y, width, height) {
    if (!pages[pageIndex].hotspots?.length) pages[pageIndex].hotspots = [];
    var hotspotIndex = pages[pageIndex].hotspots.length;
    var hotspot = {
      left: x,
      top: y,
      width: width,
      height: height,
      title: title,
      url: link,
    };
    pages[pageIndex].hotspots.push(hotspot);
    var pageDiv = params.target.querySelector(".pdf2web-page-" + (pageIndex + 1));
    currentHotspot = insertHotspotElement(pageDiv, hotspot, pageIndex, hotspotIndex);
    currentHotspot.classList.add("new");
    return hotspotIndex;
  }

  function handleHotspotCreation(e) {
    if (e.target.tagName !== "IMG") return;
    e.preventDefault();

    var pageIndex = currentPage - 1;
    var parent = params.target.querySelector(".pdf2web-page-" + currentPage);

    var isDragging = true;
    var hotspotIndex = -1;
    var initX, initY, x, y, width, height;

    const parentRect = parent.getBoundingClientRect();
    initX = toPerc(e.clientX - parentRect.left, parentRect.width);
    initY = toPerc(e.clientY - parentRect.top, parentRect.height);

    document.addEventListener("mousemove", onHotspotCreateMouseMove);
    document.addEventListener("mouseup", onHotspotCreateMouseUp);

    function onHotspotCreateMouseMove(e) {
      if (!isDragging) return;

      var percX = inRange(toPerc(e.clientX - parentRect.left, parentRect.width), 0, 100);
      var percY = inRange(toPerc(e.clientY - parentRect.top, parentRect.height), 0, 100);
      if (initX < percX) {
        x = initX;
        width = inRange(percX - initX, 0, 100);
      } else {
        x = percX;
        width = inRange(initX - percX, 0, 100);
      }
      if (initY < percY) {
        y = initY;
        height = inRange(percY - initY, 0, 100);
      } else {
        y = percY;
        height = inRange(initY - percY, 0, 100);
      }

      if (hotspotIndex < 0)
        hotspotIndex = addHotspotToPage(
          pageIndex,
          "New Hotspot",
          "https://",
          x.toFixed(2),
          y.toFixed(2),
          width.toFixed(2),
          height.toFixed(2)
        );
      pages[pageIndex].hotspots[hotspotIndex].left = x.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].top = y.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].width = width.toFixed(2);
      pages[pageIndex].hotspots[hotspotIndex].height = height.toFixed(2);
      currentHotspot.style.left = `${x}%`;
      currentHotspot.style.top = `${y}%`;
      currentHotspot.style.width = `${width}%`;
      currentHotspot.style.height = `${height}%`;
    }

    function setHotspotMinSize(hotspotELement, pageIndex, hotspotIndex) {
      var width = inRange(pages[pageIndex].hotspots[hotspotIndex].width, 5, 100);
      var height = inRange(pages[pageIndex].hotspots[hotspotIndex].height, 5, 100);
      pages[pageIndex].hotspots[hotspotIndex].width = width;
      pages[pageIndex].hotspots[hotspotIndex].height = height;
      currentHotspot.style.width = `${width}%`;
      currentHotspot.style.height = `${height}%`;
    }

    function onHotspotCreateMouseUp(e) {
      isDragging = false;
      document.removeEventListener("mousemove", onHotspotCreateMouseMove);
      document.removeEventListener("mouseup", onHotspotCreateMouseUp);
      if (hotspotIndex >= 0) {
        currentHotspot = null;
        updateHotspotList();
        editHotspot(pageIndex, hotspotIndex);
        setHotspotMinSize(currentHotspot, pageIndex, hotspotIndex);
      } else {
        cancelHotspotEdit(e);
      }
    }
  }

  function toPerc(value, total) {
    return (value / total) * 100;
  }

  function inRange(value, min, max) {
    if (value < min) return min;
    if (value > max) return max;
    return value;
  }

  function isSameHostname(url) {
    const currentHostname = window.location.hostname;
    try {
        const targetUrl = new URL(url);
        return targetUrl.hostname === currentHostname;
    } catch (error) {
        return false;
    }
  }

  function goToPageFromHash() {
      const hash = window.location.hash;
      const match = hash.match(/#page(\d+)/);
      if (match) {
          const pageNumber = parseInt(match[1], 10);
          goToPage(pageNumber);
      }
  }

  function attachHashChangeHandler() {
    window.addEventListener('hashchange', goToPageFromHash);
    goToPageFromHash();
  }
}
