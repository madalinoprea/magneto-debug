
/*
 Copyright (c) symfony/web-profiler-bundle https://symfony.com
 Code licensed under the MIT License
 */

Sfjs = (function() {
    "use strict";

    var classListIsSupported = 'classList' in document.documentElement;

    if (classListIsSupported) {
        var hasClass = function (el, cssClass) { return el.classList.contains(cssClass); };
        var removeClass = function(el, cssClass) { el.classList.remove(cssClass); };
        var addClass = function(el, cssClass) { el.classList.add(cssClass); };
        var toggleClass = function(el, cssClass) { el.classList.toggle(cssClass); };
    } else {
        var hasClass = function (el, cssClass) { return el.className.match(new RegExp('\\b' + cssClass + '\\b')); };
        var removeClass = function(el, cssClass) { el.className = el.className.replace(new RegExp('\\b' + cssClass + '\\b'), ' '); };
        var addClass = function(el, cssClass) { if (!hasClass(el, cssClass)) { el.className += " " + cssClass; } };
        var toggleClass = function(el, cssClass) { hasClass(el, cssClass) ? removeClass(el, cssClass) : addClass(el, cssClass); };
    }

    var noop = function() {},

        collectionToArray = function (collection) {
            var length = collection.length || 0,
                results = new Array(length);

            while (length--) {
                results[length] = collection[length];
            }

            return results;
        },

        profilerStorageKey = 'sf2/profiler/',

        request = function(url, onSuccess, onError, payload, options) {
            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            options = options || {};
            options.maxTries = options.maxTries || 0;
            xhr.open(options.method || 'GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function(state) {
                if (4 !== xhr.readyState) {
                    return null;
                }

                if (xhr.status == 404 && options.maxTries > 1) {
                    setTimeout(function(){
                        options.maxTries--;
                        request(url, onSuccess, onError, payload, options);
                    }, 500);

                    return null;
                }

                if (200 === xhr.status) {
                    (onSuccess || noop)(xhr);
                } else {
                    (onError || noop)(xhr);
                }
            };
            xhr.send(payload || '');
        },

        getPreference = function(name) {
            if (!window.localStorage) {
                return null;
            }

            return localStorage.getItem(profilerStorageKey + name);
        },

        setPreference = function(name, value) {
            if (!window.localStorage) {
                return null;
            }

            localStorage.setItem(profilerStorageKey + name, value);
        },

        requestStack = [],

        renderAjaxRequests = function() {
            var requestCounter = document.querySelectorAll('.sf-toolbar-ajax-requests');
            if (!requestCounter.length) {
                return;
            }

            var ajaxToolbarPanel = document.querySelector('.sf-toolbar-block-ajax');
            var tbodies = document.querySelectorAll('.sf-toolbar-ajax-request-list');
            var state = 'ok';
            if (tbodies.length) {
                var tbody = tbodies[0];

                var rows = document.createDocumentFragment();

                if (requestStack.length) {
                    for (var i = 0; i < requestStack.length; i++) {
                        var request = requestStack[i];

                        var row = document.createElement('tr');
                        rows.appendChild(row);

                        var methodCell = document.createElement('td');
                        if (request.error) {
                            methodCell.className = 'sf-ajax-request-error';
                        }
                        methodCell.textContent = request.method;
                        row.appendChild(methodCell);

                        var pathCell = document.createElement('td');
                        pathCell.className = 'sf-ajax-request-url';
                        if ('GET' === request.method) {
                            var pathLink = document.createElement('a');
                            pathLink.setAttribute('href', request.url);
                            pathLink.textContent = request.url;
                            pathCell.appendChild(pathLink);
                        } else {
                            pathCell.textContent = request.url;
                        }
                        pathCell.setAttribute('title', request.url);
                        row.appendChild(pathCell);

                        var durationCell = document.createElement('td');
                        durationCell.className = 'sf-ajax-request-duration';

                        if (request.duration) {
                            durationCell.textContent = request.duration + "ms";
                        } else {
                            durationCell.textContent = '-';
                        }
                        row.appendChild(durationCell);

                        row.appendChild(document.createTextNode(' '));
                        var profilerCell = document.createElement('td');

                        if (request.profilerUrl) {
                            var profilerLink = document.createElement('a');
                            profilerLink.setAttribute('href', request.profilerUrl);
                            profilerLink.textContent = request.profile;
                            profilerCell.appendChild(profilerLink);
                        } else {
                            profilerCell.textContent = 'n/a';
                        }

                        row.appendChild(profilerCell);

                        var requestState = 'ok';
                        if (request.error) {
                            requestState = 'error';
                            if (state != "loading" && i > requestStack.length - 4) {
                                state = 'error';
                            }
                        } else if (request.loading) {
                            requestState = 'loading';
                            state = 'loading';
                        }
                        row.className = 'sf-ajax-request sf-ajax-request-' + requestState;
                    }

                    var infoSpan = document.querySelectorAll(".sf-toolbar-ajax-info")[0];
                    var children = collectionToArray(tbody.children);
                    for (var i = 0; i < children.length; i++) {
                        tbody.removeChild(children[i]);
                    }
                    tbody.appendChild(rows);

                    if (infoSpan) {
                        var text = requestStack.length + ' AJAX request' + (requestStack.length > 1 ? 's' : '');
                        infoSpan.textContent = text;
                    }

                    ajaxToolbarPanel.style.display = 'block';
                } else {
                    ajaxToolbarPanel.style.display = 'none';
                }
            }

            requestCounter[0].textContent = requestStack.length;

            var className = 'sf-toolbar-ajax-requests sf-toolbar-value';
            requestCounter[0].className = className;

            if (state == 'ok') {
                Sfjs.removeClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
                Sfjs.removeClass(ajaxToolbarPanel, 'sf-toolbar-status-red');
            } else if (state == 'error') {
                Sfjs.addClass(ajaxToolbarPanel, 'sf-toolbar-status-red');
                Sfjs.removeClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
            } else {
                Sfjs.addClass(ajaxToolbarPanel, 'sf-ajax-request-loading');
            }
        };

    var addEventListener;

    var el = document.createElement('div');
    if (!'addEventListener' in el) {
        addEventListener = function (element, eventName, callback) {
            element.attachEvent('on' + eventName, callback);
        };
    } else {
        addEventListener = function (element, eventName, callback) {
            element.addEventListener(eventName, callback, false);
        };
    }


    return {
        hasClass: hasClass,

        removeClass: removeClass,

        addClass: addClass,

        toggleClass: toggleClass,

        getPreference: getPreference,

        setPreference: setPreference,

        addEventListener: addEventListener,

        request: request,

        renderAjaxRequests: renderAjaxRequests,

        load: function(selector, url, onSuccess, onError, options) {
            var el = document.getElementById(selector);

            if (el && el.getAttribute('data-sfurl') !== url) {
                request(
                    url,
                    function(xhr) {
                        el.innerHTML = xhr.responseText;
                        el.setAttribute('data-sfurl', url);
                        removeClass(el, 'loading');
                        (onSuccess || noop)(xhr, el);
                    },
                    function(xhr) { (onError || noop)(xhr, el); },
                    '',
                    options
                );
            }

            return this;
        },

        toggle: function(selector, elOn, elOff) {
            var tmp = elOn.style.display,
                el = document.getElementById(selector);

            elOn.style.display = elOff.style.display;
            elOff.style.display = tmp;

            if (el) {
                el.style.display = 'none' === tmp ? 'none' : 'block';
            }

            return this;
        },

        createTabs: function() {
            var tabGroups = document.querySelectorAll('.sf-tabs');

            /* create the tab navigation for each group of tabs */
            for (var i = 0; i < tabGroups.length; i++) {
                var tabs = tabGroups[i].querySelectorAll('.tab');
                var tabNavigation = document.createElement('ul');
                tabNavigation.className = 'tab-navigation';

                for (var j = 0; j < tabs.length; j++) {
                    var tabId = 'tab-' + i + '-' + j;
                    var tabTitle = tabs[j].querySelector('.tab-title').innerHTML;

                    var tabNavigationItem = document.createElement('li');
                    tabNavigationItem.setAttribute('data-tab-id', tabId);
                    if (j == 0) { Sfjs.addClass(tabNavigationItem, 'active'); }
                    if (Sfjs.hasClass(tabs[j], 'disabled')) { Sfjs.addClass(tabNavigationItem, 'disabled'); }
                    tabNavigationItem.innerHTML = tabTitle;
                    tabNavigation.appendChild(tabNavigationItem);

                    var tabContent = tabs[j].querySelector('.tab-content');
                    tabContent.parentElement.setAttribute('id', tabId);
                }

                tabGroups[i].insertBefore(tabNavigation, tabGroups[i].firstChild);
            }

            /* display the active tab and add the 'click' event listeners */
            for (i = 0; i < tabGroups.length; i++) {
                tabNavigation = tabGroups[i].querySelectorAll('.tab-navigation li');

                for (j = 0; j < tabNavigation.length; j++) {
                    tabId = tabNavigation[j].getAttribute('data-tab-id');
                    document.getElementById(tabId).querySelector('.tab-title').className = 'hidden';

                    if (Sfjs.hasClass(tabNavigation[j], 'active')) {
                        document.getElementById(tabId).className = 'block';
                    } else {
                        document.getElementById(tabId).className = 'hidden';
                    }

                    tabNavigation[j].addEventListener('click', function(e) {
                        var activeTab = e.target || e.srcElement;

                        /* needed because when the tab contains HTML contents, user can click */
                        /* on any of those elements instead of their parent '<li>' element */
                        while (activeTab.tagName.toLowerCase() !== 'li') {
                            activeTab = activeTab.parentNode;
                        }

                        /* get the full list of tabs through the parent of the active tab element */
                        var tabNavigation = activeTab.parentNode.children;
                        for (var k = 0; k < tabNavigation.length; k++) {
                            var tabId = tabNavigation[k].getAttribute('data-tab-id');
                            document.getElementById(tabId).className = 'hidden';
                            Sfjs.removeClass(tabNavigation[k], 'active');
                        }

                        Sfjs.addClass(activeTab, 'active');
                        var activeTabId = activeTab.getAttribute('data-tab-id');
                        document.getElementById(activeTabId).className = 'block';
                    });
                }
            }
        },

        createToggles: function() {
            var toggles = document.querySelectorAll('.sf-toggle');

            for (var i = 0; i < toggles.length; i++) {
                var elementSelector = toggles[i].getAttribute('data-toggle-selector');
                var element = document.querySelector(elementSelector);

                Sfjs.addClass(element, 'sf-toggle-content');

                if (toggles[i].hasAttribute('data-toggle-initial') && toggles[i].getAttribute('data-toggle-initial') == 'display') {
                    Sfjs.addClass(element, 'sf-toggle-visible');
                } else {
                    Sfjs.addClass(element, 'sf-toggle-hidden');
                }

                Sfjs.addEventListener(toggles[i], 'click', function(e) {
                    e.preventDefault();

                    var toggle = e.target || e.srcElement;

                    /* needed because when the toggle contains HTML contents, user can click */
                    /* on any of those elements instead of their parent '.sf-toggle' element */
                    while (!Sfjs.hasClass(toggle, 'sf-toggle')) {
                        toggle = toggle.parentNode;
                    }

                    var element = document.querySelector(toggle.getAttribute('data-toggle-selector'));

                    Sfjs.toggleClass(element, 'sf-toggle-hidden');
                    Sfjs.toggleClass(element, 'sf-toggle-visible');

                    /* the toggle doesn't change its contents when clicking on it */
                    if (!toggle.hasAttribute('data-toggle-alt-content')) {
                        return;
                    }

                    if (!toggle.hasAttribute('data-toggle-original-content')) {
                        toggle.setAttribute('data-toggle-original-content', toggle.innerHTML);
                    }

                    var currentContent = toggle.innerHTML;
                    var originalContent = toggle.getAttribute('data-toggle-original-content');
                    var altContent = toggle.getAttribute('data-toggle-alt-content');
                    toggle.innerHTML = currentContent !== altContent ? altContent : originalContent;
                });
            }
        }
    };
})();

Sfjs.addEventListener(window, 'load', function() {
    Sfjs.createTabs();
    Sfjs.createToggles();
});

/**
 * Based on symfony's explain()
 *
 * @param link
 * @returns {boolean}
 */
function fetchData(link) {
    "use strict";

    var targetId = link.getAttribute('data-target-id');
    var targetElement = document.getElementById(targetId);

    if (targetElement.style.display != 'block') {
        Sfjs.load(targetId, link.href, function() {
                if (hljs) {
                    var codeSnippets = document.getElementsByClassName('lazy-code-snippet');
                    for (var i=0; i<codeSnippets.length; i++) {
                        hljs.highlightBlock(codeSnippets[i]);
                    }
                }
            },
            function(xhr, el) {
                el.innerHTML = 'An error occurred while loading query.';
            }
        );

        targetElement.style.display = 'block';
        link.innerHTML = 'Hide';
    } else {
        targetElement.style.display = 'none';
        link.innerHTML = link.title;
    }

    return false;
}


function sortTable(header, column, targetId) {
    "use strict";

    var direction = parseInt(header.getAttribute('data-sort-direction')) || 1,
        items = [],
        target = document.getElementById(targetId),
        rows = target.children,
        headers = header.parentElement.children,
        i;

    for (i = 0; i < rows.length; ++i) {
        items.push(rows[i]);
    }

    for (i = 0; i < headers.length; ++i) {
        headers[i].removeAttribute('data-sort-direction');
        if (headers[i].children.length > 0) {
            headers[i].children[0].innerHTML = '';
        }
    }

    header.setAttribute('data-sort-direction', (-1*direction).toString());
    header.children[0].innerHTML = direction > 0 ? '<span class="text-muted">&#9650;</span>' : '<span class="text-muted">&#9660;</span>';

    items.sort(function(a, b) {
        return direction * (parseFloat(a.children[column].innerHTML) - parseFloat(b.children[column].innerHTML));
    });

    for (i = 0; i < items.length; ++i) {
        Sfjs.removeClass(items[i], i % 2 ? 'even' : 'odd');
        Sfjs.addClass(items[i], i % 2 ? 'odd' : 'even');
        target.appendChild(items[i]);
    }
}

/**
 * In-memory key-value cache manager
 */
var cache = new function () {
    "use strict";
    var dict = {};

    this.get = function (key) {
        return dict.hasOwnProperty(key)
            ? dict[key]
            : null;
    };

    this.set = function (key, value) {
        dict[key] = value;

        return value;
    };
};

/**
 * Query an element with a CSS selector.
 *
 * @param string selector a CSS-selector-compatible query string.
 *
 * @return DOMElement|null
 */
function query(selector) {
    "use strict";
    var key = 'SELECTOR: ' + selector;

    return cache.get(key) || cache.set(key, document.querySelector(selector));
}

/**
 * Canvas Manager
 */
function CanvasManager(requests, maxRequestTime) {
    "use strict";

    var _drawingColors = {
            "default": "#999",
            "section": "#444",
            "event_listener": "#00B8F5",
            "event_listener_loading": "#00B8F5",
            "template": "#66CC00",
            "doctrine": "#FF6633",
            "propel": "#FF6633"
        },
        _storagePrefix = 'timeline/',
        _threshold = 1,
        _requests = requests,
        _maxRequestTime = maxRequestTime;

    /**
     * Check whether this event is a child event.
     *
     * @return true if it is.
     */
    function isChildEvent(event) {
        return '__section__.child' === event.name;
    }

    /**
     * Check whether this event is categorized in 'section'.
     *
     * @return true if it is.
     */
    function isSectionEvent(event) {
        return 'section' === event.category;
    }

    /**
     * Get the width of the container.
     */
    function getContainerWidth() {
        return query('#collector-content h2').clientWidth;
    }

    /**
     * Draw one canvas.
     *
     * @param request   the request object
     * @param max       <subjected for removal>
     * @param threshold the threshold (lower bound) of the length of the timeline (in milliseconds).
     * @param width     the width of the canvas.
     */
    this.drawOne = function (request, max, threshold, width) {
        "use strict";
        var text,
            ms,
            xc,
            drawableEvents,
            mainEvents,
            elementId = 'timeline_' + request.id,
            canvasHeight = 0,
            gapPerEvent = 38,
            colors = _drawingColors,
            space = 10.5,
            ratio = (width - space * 2) / max,
            h = space,
            x = request.left * ratio + space, // position
            canvas = cache.get(elementId) || cache.set(elementId, document.getElementById(elementId)),
            ctx = canvas.getContext("2d"),
            scaleRatio,
            devicePixelRatio;

        // Filter events whose total time is below the threshold.
        drawableEvents = request.events.filter(function (event) {
            return event.duration >= threshold;
        });

        canvasHeight += gapPerEvent * drawableEvents.length;

        // For retina displays so text and boxes will be crisp
        devicePixelRatio = window.devicePixelRatio == "undefined" ? 1 : window.devicePixelRatio;
        scaleRatio = devicePixelRatio / 1;

        canvas.width = width * scaleRatio;
        canvas.height = canvasHeight * scaleRatio;

        canvas.style.width = width + 'px';
        canvas.style.height = canvasHeight + 'px';

        ctx.scale(scaleRatio, scaleRatio);

        ctx.textBaseline = "middle";
        ctx.lineWidth = 0;

        // For each event, draw a line.
        ctx.strokeStyle = "#CCC";

        drawableEvents.forEach(function (event) {
            event.periods.forEach(function (period) {
                var timelineHeadPosition = x + period.start * ratio;

                if (isChildEvent(event)) {
                    /* create a striped background dynamically */
                    var img = new Image();
                    img.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKBAMAAAB/HNKOAAAAIVBMVEX////w8PDd7h7d7h7d7h7d7h7w8PDw8PDw8PDw8PDw8PAOi84XAAAAKUlEQVQImWNI71zAwMBQMYuBgY0BxExnADErGEDMTgYQE8hnAKtCZwIAlcMNSR9a1OEAAAAASUVORK5CYII=';
                    var pattern = ctx.createPattern(img, 'repeat');

                    ctx.fillStyle = pattern;
                    ctx.fillRect(timelineHeadPosition, 0, (period.end - period.start) * ratio, canvasHeight);
                } else if (isSectionEvent(event)) {
                    var timelineTailPosition = x + period.end * ratio;

                    ctx.beginPath();
                    ctx.moveTo(timelineHeadPosition, 0);
                    ctx.lineTo(timelineHeadPosition, canvasHeight);
                    ctx.moveTo(timelineTailPosition, 0);
                    ctx.lineTo(timelineTailPosition, canvasHeight);
                    ctx.fill();
                    ctx.closePath();
                    ctx.stroke();
                }
            });
        });

        // Filter for main events.
        mainEvents = drawableEvents.filter(function (event) {
            return !isChildEvent(event)
        });

        // For each main event, draw the visual presentation of timelines.
        mainEvents.forEach(function (event) {

            h += 8;

            // For each sub event, ...
            event.periods.forEach(function (period) {
                // Set the drawing style.
                ctx.fillStyle = colors['default'];
                ctx.strokeStyle = colors['default'];

                if (colors[event.name]) {
                    ctx.fillStyle = colors[event.name];
                    ctx.strokeStyle = colors[event.name];
                } else if (colors[event.category]) {
                    ctx.fillStyle = colors[event.category];
                    ctx.strokeStyle = colors[event.category];
                }

                // Draw the timeline
                var timelineHeadPosition = x + period.start * ratio;

                if (!isSectionEvent(event)) {
                    ctx.fillRect(timelineHeadPosition, h + 3, 2, 8);
                    ctx.fillRect(timelineHeadPosition, h, (period.end - period.start) * ratio || 2, 6);
                } else {
                    var timelineTailPosition = x + period.end * ratio;

                    ctx.beginPath();
                    ctx.moveTo(timelineHeadPosition, h);
                    ctx.lineTo(timelineHeadPosition, h + 11);
                    ctx.lineTo(timelineHeadPosition + 8, h);
                    ctx.lineTo(timelineHeadPosition, h);
                    ctx.fill();
                    ctx.closePath();
                    ctx.stroke();

                    ctx.beginPath();
                    ctx.moveTo(timelineTailPosition, h);
                    ctx.lineTo(timelineTailPosition, h + 11);
                    ctx.lineTo(timelineTailPosition - 8, h);
                    ctx.lineTo(timelineTailPosition, h);
                    ctx.fill();
                    ctx.closePath();
                    ctx.stroke();

                    ctx.beginPath();
                    ctx.moveTo(timelineHeadPosition, h);
                    ctx.lineTo(timelineTailPosition, h);
                    ctx.lineTo(timelineTailPosition, h + 2);
                    ctx.lineTo(timelineHeadPosition, h + 2);
                    ctx.lineTo(timelineHeadPosition, h);
                    ctx.fill();
                    ctx.closePath();
                    ctx.stroke();
                }
            });

            h += 30;

            ctx.beginPath();
            ctx.strokeStyle = "#E0E0E0";
            ctx.moveTo(0, h - 10);
            ctx.lineTo(width, h - 10);
            ctx.closePath();
            ctx.stroke();
        });

        h = space;

        // For each event, draw the label.
        mainEvents.forEach(function (event) {

            ctx.fillStyle = "#444";
            ctx.font = "12px sans-serif";
            text = event.name;
            ms = "  " + (event.duration < 1 ? event.duration : parseInt(event.duration, 10)) + " ms / " + event.memory + " MB";
            if (x + event.starttime * ratio + ctx.measureText(text + ms).width > width) {
                ctx.textAlign = "end";
                ctx.font = "10px sans-serif";
                ctx.fillStyle = "#777";
                xc = x + event.endtime * ratio - 1;
                ctx.fillText(ms, xc, h);

                xc -= ctx.measureText(ms).width;
                ctx.font = "12px sans-serif";
                ctx.fillStyle = "#222";
                ctx.fillText(text, xc, h);
            } else {
                ctx.textAlign = "start";
                ctx.font = "13px sans-serif";
                ctx.fillStyle = "#222";
                xc = x + event.starttime * ratio + 1;
                ctx.fillText(text, xc, h);

                xc += ctx.measureText(text).width;
                ctx.font = "11px sans-serif";
                ctx.fillStyle = "#777";
                ctx.fillText(ms, xc, h);
            }

            h += gapPerEvent;
        });
    };

    this.drawAll = function (width, threshold) {
        "use strict";

        width = width || getContainerWidth();
        threshold = threshold || this.getThreshold();

        var self = this;

        _requests.forEach(function (request) {
            self.drawOne(request, _maxRequestTime, threshold, width);
        });
    };

    this.getThreshold = function () {
        var threshold = Sfjs.getPreference(_storagePrefix + 'threshold');

        if (null === threshold) {
            return _threshold;
        }

        _threshold = parseInt(threshold);

        return _threshold;
    };

    this.setThreshold = function (threshold) {
        _threshold = threshold;

        Sfjs.setPreference(_storagePrefix + 'threshold', threshold);

        return this;
    };
}

function canvasAutoUpdateOnResizeAndSubmit(e) {
    e.preventDefault();
    canvasManager.drawAll();
}

function canvasAutoUpdateOnThresholdChange(e) {
    canvasManager
        .setThreshold(query('input[name="threshold"]').value)
        .drawAll();
}
