/**
 * body要素に、ユーザーのOS・デバイスとブラウザを判定してクラスとして付与する
 * OS・デバイス: iphone, ipad, android, androidphone, androidtablet, windows, mac
 * ブラウザ: edge, chrome, firefox, safari
 * @function
 * @name addDeviceBrowserClasses
 * @returns {void}
 */
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const ua = window.navigator.userAgent.toLowerCase(),
        isIOS = /iphone|ipad|ipod/.test(ua),
        isMac = /macintosh|mac os x/.test(ua),
        isAndroid = /android/.test(ua),
        isWindows = /windows/.test(ua),
        isEdge = /edg/.test(ua),
        isChrome = /chrome|crios/.test(ua),
        isFirefox = /firefox/.test(ua),
        isSafari = /safari/.test(ua);

    let classArr = [];

    // プラットフォーム判定
    if (isIOS) {
        if (/iphone/.test(ua)) {
            classArr.push('iphone');
        } else if (/ipad/.test(ua)) {
            classArr.push('ipad');
        }
    } else if (isMac && 'ontouchend' in document) {
        classArr.push('ipad');
    } else if (isAndroid) {
        classArr.push('android');

        if (/mobile/.test(ua)) {
            classArr.push('androidphone');
        } else if (/tablet/.test(ua)) {
            classArr.push('androidtablet');
        }
    } else if (isWindows) {
        classArr.push('windows');
    } else if (isMac) {
        classArr.push('mac');
    }

    // ブラウザ判定
    if (isEdge) {
        classArr.push('edge');
    } else if (isChrome) {
        classArr.push('chrome');
    } else if (isFirefox) {
        classArr.push('firefox');
    } else if (isSafari) {
        classArr.push('safari');
    }

    document.body.classList.add(...classArr);
});

/**
 * スムーススクロールを初期化し、指定されたトリガー要素がクリックされたときにスクロールを実行します。
 * @function
 * @name initSmoothScroll
 * @returns {void}
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const trigger = document.querySelectorAll('a[href^="#"]'); //トリガー要素

    for (let i = 0; i < trigger.length; i++) {
        trigger[i].addEventListener('click', function (e) {
            e.preventDefault();

            const href = this.getAttribute('href'); // href値
            let scrollPos; //スムーススクロールする位置

            if (href === '#') {
                scrollPos = 0;
            } else {
                const target = document.getElementById(href.replace('#', '')); // ターゲット要素
                if (target == null) return;
                const targetY = target.getBoundingClientRect().top, // ターゲット要素の垂直位置
                    currentY = window.pageYOffset; // スクロール量

                scrollPos = targetY + currentY;
            }

            window.scrollTo({
                top: scrollPos,
                behavior: 'smooth'
            });
        });
    }
});
