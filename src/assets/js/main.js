document.addEventListener('DOMContentLoaded', () => {
    // --- Language Switcher Logic ---

    const translations = {
        ja: {
            hero_sub: 'Beyond the Genuine Quality',
            concept_main: '「純正超え」の<br>感動品質を。',
            concept_text:
                '量産車にはない「あなただけ」の特別感。<br>私たちは、素材選びからステッチひとつに至るまで、妥協なきクラフトマンシップで理想の空間を具現化します。<br>最新の欧州車に見られるトレンドを取り入れつつ、日本の職人技で仕上げる。<br>それが、技巧 -Giko- の流儀です。',
            ba_desc:
                '長年の使用で劣化したシートも、技巧-Giko-の手にかかれば新品以上の輝きを取り戻します。<br>スライダーを動かして、その変化をご覧ください。',
            ba_before_title: 'BEFORE STATE',
            ba_before_text:
                '経年劣化によるひび割れ、色褪せ、ウレタンのへたりが見られる状態。\n革本来のツヤは失われ、表面のコーティングも剥がれています。',
            ba_after_title: 'AFTER FINISH',
            ba_after_text:
                '最高級ナッパレザーを使用し、純正同様のステッチラインで張り替え。\nウレタンの補修も行い、新車時のような座り心地と、芳醇な革の香りが復活しました。',
            material_main: '手に触れる全てに、<br>最高級の悦びを。',
            material_sub1_title: '厳選された本革',
            material_sub1_text:
                '欧州の高級車にも採用されるナッパレザーをはじめ、耐久性と質感に優れた最高ランクの原皮のみを厳選。\n時を経るごとに馴染み、深みを増す本物の質感をお楽しみください。',
            material_sub2_title: '多種多様なマテリアル',
            material_sub2_text:
                'アルカンターラ、パンチングレザー、カーボンレザーなど、デザイン性と機能性を両立する多彩な素材をご用意。\nステッチの糸一本の色に至るまで、数千通りの組み合わせが可能です。',
            flow_sub: 'ご納車までの流れ',
            flow_step1_title: 'お問い合わせ',
            flow_step1_text:
                'まずはお電話かメールにてご連絡ください。車種やご希望のイメージをお伺いします。',
            flow_step2_title: 'プランニング',
            flow_step2_text:
                'サンプルをご覧いただきながら、詳細なデザインや素材を決定します。',
            flow_step3_title: '施工・製作',
            flow_step3_text:
                '熟練の職人が一針一針、丁寧に仕上げていきます。進捗のご報告も可能です。',
            flow_step4_title: 'ご納車',
            flow_step4_text:
                '仕上がりをご確認いただき、納車となります。アフターフォローもお任せください。',
            contact_intro:
                'お見積もりのご依頼、施工に関するご相談など、お気軽にお問い合わせください。<br>内容を確認次第、担当者よりご連絡させていただきます。',

            ba_page_main_title: '劇的な変化を、その目で。',
            ba_page_main_desc:
                '長年の使用で劣化したシートも、技巧-Giko-の手にかかれば新品以上の輝きを取り戻します。<br>スライダーを動かして、その変化をご覧ください。',
            ba_page_before_desc:
                '経年劣化によるひび割れ、色褪せ、ウレタンのへたりが見られる状態。<br>革本来のツヤは失われ、表面のコーティングも剥がれています。',
            ba_page_after_desc:
                '最高級ナッパレザーを使用し、純正同様のステッチラインで張り替え。<br>ウレタンの補修も行い、新車時のような座り心地と、芳醇な革の香りが復活しました。',
            '404_text':
                'お探しのページは見つかりませんでした。<br>削除されたか、URLが変更された可能性があります。',

            // Product Pages
            alphard_concept_text:
                '純正の高級感をさらに昇華させた、フルホワイトレザーのカスタムインテリアです。オーナー様のご要望により、清潔感と広がりを感じさせるピュアホワイトのナッパレザーを全面に使用。アクセントとして、ドアトリムやセンターコンソールにはダイヤモンドステッチを施し、立体感と高級感を演出しました。<br><br>ステッチにはシルバーグレーを採用し、主張しすぎない洗練されたコントラストを実現。長時間のドライブでも疲れにくいよう、クッション材の調整も行っています。まさに「動くリビング」と呼ぶにふさわしい、至高のプライベート空間が完成しました。',
            alphard_data_content:
                '全席シート張り替え<br>ドアトリム張り替え<br>天井ルーフライニング<br>フロアマット製作<br>ステアリング巻き替え',

            gtr32_concept_text:
                '経年劣化により痛みの激しかったR32 GT-Rの内装を、当時の雰囲気を残しつつ現代の素材でリフレッシュしました。<br>純正シートの形状を崩さないよう、ウレタンの補修から徹底的に実施。使用したのは、耐久性と質感に優れた自動車用本革。パンチング加工を施すことで、スポーティな印象と通気性を確保しています。<br><br>ステアリングやシフトノブも同色のレザーで巻き替え、車内全体の統一感を高めました。伝説の名車にふさわしい、重厚感と機能性を兼ね備えたインテリアへと生まれ変わりました。',
            gtr32_data_content:
                'フロント/リアシート張り替え<br>ウレタンフォーム補修・形成<br>ステアリング巻き替え',

            mrs_concept_text:
                'ライトウェイトスポーツカーMR-Sのシートを、鮮烈なレッドレザーとブラックのコンビネーションでカスタム。<br>オープン走行時の「見られる」ことを意識し、ヘッドレストやサイドサポートのラインにこだわりました。使用したレザーは、雨や日光にも強い自動車専用スペックの合成皮革。耐久性を確保しつつ、本革に劣らない質感を追求しました。<br><br>内装全体のブラック樹脂パーツとのコントラストが美しく、ドアを開けた瞬間、そして走り出す瞬間の高揚感を演出します。',
            mrs_data_content:
                '運転席・助手席シート張り替え<br>ドアアームレスト張り替え',

            sl55_concept_text:
                'ハイパフォーマンスオープンカーSL55 AMGの内装を、明るいベージュのナッパレザーで一新。<br>純正のブラック基調から、開放感のあるエレガントな空間へと生まれ変わらせました。同時に、ドアスピーカーのアウターバッフル化やツイーター埋め込み加工など、本格的なオーディオカスタムも実施。<br><br>見た目の美しさだけでなく、オープン走行時でもクリアに音楽を楽しめる音響空間を構築しました。視覚と聴覚の両方で、極上のドライブ体験を提供します。',
            sl55_data_content:
                'シート・ドアトリム張り替え<br>Aピラーツイーター埋め込み<br>ドアスピーカーアウターバッフル製作',

            vclass_concept_text:
                '広大な室内空間を持つVクラスを、エグゼクティブのための移動オフィス兼ラウンジへと改装。<br>後席にはオットマン付きの独立キャプテンシートを採用し、ファーストクラスのような座り心地を実現しました。シート表皮には肌触りの良いナッパレザーを使用し、キルティングパターンで高級感を演出。<br><br>さらに、プライバシーを守るためのパーティション設置や、アンビエントライトの追加により、昼夜を問わずリラックスできる空間を構築。ビジネスシーンからプライベートの移動まで、最上のくつろぎを提供します。',
            vclass_data_content:
                '後席キャプテンシート換装<br>フロアカーペット製作<br>アンビエントライト追加<br>ドアトリム・天井レザー張り',

            avensis_concept_text:
                '長年の使用で擦れや破れが生じていた純正シートを、部分的に張り替えることで新車時の美しさを取り戻しました。<br>ダメージの大きいサイドサポート部分のみを、純正の風合いに近い新しいレザーでリペア。コストを抑えつつ、車内全体の印象を劇的に改善する、賢い選択肢としての「リペア」をご提案しました。<br><br>ウレタンの補充も同時に行い、へたっていた座り心地も改善。愛着のある一台を、これからも長く大切に乗り続けるためのメンテナンスメニューです。',
            avensis_data_content:
                '運転席サイドサポート部分張り替え<br>ウレタン補充・成形',

            price_note: '※参考価格'
        },
        en: {
            hero_sub: 'Beyond the Genuine Quality',
            concept_main: 'Quality Beyond<br>the Genuine.',
            concept_text:
                'A special feeling that mass-produced cars cannot offer.<br>We realize your ideal space with uncompromising craftsmanship, from material selection to every single stitch.<br>Incorporating the latest European trends, finished with Japanese craftsmanship.<br>That is the Giko style.',
            ba_desc:
                'Even seats deteriorated over years of use regain a shine better than new in the hands of Giko.<br>Move the slider to see the transformation.',
            ba_before_title: 'BEFORE STATE',
            ba_before_text:
                'State showing cracking, fading, and urethane collapse due to aging.\nThe original leather sheen is lost, and the surface coating is peeling.',
            ba_after_title: 'AFTER FINISH',
            ba_after_text:
                'Reupholstered using premium Nappa leather with stitch lines matching the original.\nUrethane repair is also performed, restoring the seating comfort of a new car and the rich scent of leather.',
            material_main: 'Premium Joy in<br>Everything You Touch.',
            material_sub1_title: 'Carefully Selected Leather',
            material_sub1_text:
                'We select only the highest rank raw hides with excellent durability and texture, including Nappa leather used in European luxury cars.\nEnjoy the genuine texture that adapts and gains depth over time.',
            material_sub2_title: 'Diverse Materials',
            material_sub2_text:
                'We offer a variety of materials that balance design and functionality, such as Alcantara, punched leather, and carbon leather.\nThousands of combinations are possible, down to the color of a single stitch thread.',
            flow_sub: 'Flow to Delivery',
            flow_step1_title: 'Inquiry',
            flow_step1_text:
                'Please contact us by phone or email first. We will ask about your car model and desired image.',
            flow_step2_title: 'Planning',
            flow_step2_text:
                'We will decide on detailed designs and materials while viewing samples.',
            flow_step3_title: 'Construction',
            flow_step3_text:
                'Skilled craftsmen finish it carefully, stitch by stitch. Progress reports are also possible.',
            flow_step4_title: 'Delivery',
            flow_step4_text:
                'Check the finish and we will deliver it. Leave the after-sales follow-up to us.',
            contact_intro:
                'Please feel free to contact us for quote requests or consultation regarding construction.<br>Our representative will contact you as soon as we check the content.',

            ba_page_main_title: 'Witness the dramatic transformation.',
            ba_page_main_desc:
                'Even seats deteriorated from years of use regain a shine better than new in the hands of Giko.<br>Move the slider to see the transformation.',
            ba_page_before_desc:
                'A state showing cracking, fading, and urethane collapse due to aging.<br>The original leather sheen is lost, and the surface coating is peeling.',
            ba_page_after_desc:
                'Reupholstered using premium Nappa leather with stitch lines matching the original.<br>Urethane repair was also performed, restoring the seating comfort of a new car and the rich scent of leather.',

            '404_text':
                'The page you are looking for was not found.<br>It may have been deleted or the URL may have changed.',

            // Product Pages
            alphard_concept_text:
                "A purely white leather custom interior that elevates the genuine luxury even further. At the owner's request, pure white Nappa leather is used throughout to create a clean and spacious feel. Diamond stitching on the door trims and center console adds depth and a premium touch as accents.<br><br>Silver-gray stitching is chosen for a sophisticated contrast that isn't too overpowering. We also adjusted the cushioning to ensure comfort even on long drives. A supreme private space worthy of being called a 'moving living room' has been completed.",
            alphard_data_content:
                'Full Seat Reupholstery<br>Door Trim Reupholstery<br>Ceiling Roof Lining<br>Floor Mat Production<br>Steering Wheel Rewrapping',

            gtr32_concept_text:
                'We refreshed the interior of the R32 GT-R, which had severe age-related deterioration, using modern materials while retaining the atmosphere of that era. <br>We thoroughly repaired the urethane to preserve the shape of the original seats. The material used is automotive genuine leather with excellent durability and texture. Punching processing ensures a sporty look and breathability.<br><br>The steering wheel and shift knob were also rewrapped in matching colored leather to enhance the unity of the entire interior. It has been reborn as an interior with both dignity and functionality worthy of a legendary car.',
            gtr32_data_content:
                'Front/Rear Seat Reupholstery<br>Urethane Foam Repair & Shaping<br>Steering Wheel Rewrapping',

            mrs_concept_text:
                "Customized the seats of the lightweight sports car MR-S with a striking combination of red leather and black.<br>Conscious of being 'seen' when driving with the top down, we paid close attention to the lines of the headrests and side supports. We used synthetic leather with automotive specs that is resistant to rain and sunlight. While ensuring durability, we pursued a texture comparable to genuine leather.<br><br>The contrast with the black resin parts of the interior is beautiful, creating a sense of excitement the moment you open the door and start driving.",
            mrs_data_content:
                'Driver/Passenger Seat Reupholstery<br>Door Armrest Reupholstery',

            sl55_concept_text:
                'Completely renewed the interior of the high-performance open-top SL55 AMG with bright beige Nappa leather.<br>Transformed from the original black tone to an elegant space with an open feel. At the same time, we performed full-scale audio customization, including outer baffles for door speakers and embedded tweeters.<br><br>We built an acoustic space where you can enjoy clear music even when driving with the top down, not just for visual beauty. We provide the ultimate driving experience for both your eyes and ears.',
            sl55_data_content:
                'Seat & Door Trim Reupholstery<br>A-Pillar Tweeter Embedding<br>Door Speaker Outer Baffle Production',

            vclass_concept_text:
                'Renovated the V-Class with its vast interior space into a mobile office and lounge for executives.<br>Independent captain seats with ottomans were adopted for the rear to achieve first-class comfort. Nappa leather with a great touch is used for the seat upholstery, and quilting patterns create a sense of luxury.<br><br>Furthermore, by installing a partition for privacy and adding ambient lighting, we built a relaxing space day and night. We provide the utmost comfort for everything from business scenes to private travel.',
            vclass_data_content:
                'Rear Captain Seat Replacement<br>Floor Carpet Production<br>Ambient Light Addition<br>Door Trim & Ceiling Leather Upholstery',

            avensis_concept_text:
                "Restored the beauty of a new car by partially reupholstering the genuine seats that had scuffs and tears from years of use.<br>We repaired only the heavily damaged side support parts with new leather that matches the original texture. We proposed 'repair' as a smart option to dramatically improve the impression of the interior while keeping costs down.<br><br>We also replenished the urethane to improve the worn-out seating comfort. This is a maintenance menu to continue riding your cherished car carefully for a long time.",
            avensis_data_content:
                "Driver's Seat Side Support Reupholstery<br>Urethane Replenishment & Molding",

            price_note: '* Reference Price'
        }
    };

    const updateContent = (lang) => {
        document.querySelectorAll('[data-i18n]').forEach((el) => {
            const key = el.getAttribute('data-i18n');
            if (translations[lang] && translations[lang][key]) {
                el.innerHTML = translations[lang][key].replace(/\n/g, '<br>');
            }
        });

        // Update Toggle Button Visuals
        const btns = [
            document.getElementById('lang-toggle-desktop'),
            document.getElementById('lang-toggle-mobile')
        ];
        btns.forEach((btn) => {
            if (!btn) return;
            const jpSpan = btn.children[0];
            const enSpan = btn.children[2];
            if (lang === 'ja') {
                jpSpan.classList.add('text-primary');
                jpSpan.classList.remove('text-white', 'opacity-50');
                enSpan.classList.add('text-white');
                enSpan.classList.remove('text-primary');
                // Make EN look inactive
                enSpan.style.opacity = '0.5';
                jpSpan.style.opacity = '1';
            } else {
                enSpan.classList.add('text-primary');
                enSpan.classList.remove('text-white', 'opacity-50');
                jpSpan.classList.add('text-white');
                jpSpan.classList.remove('text-primary');
                // Make JP look inactive
                jpSpan.style.opacity = '0.5';
                enSpan.style.opacity = '1';
            }
        });

        localStorage.setItem('giko_lang', lang);
        document.documentElement.lang = lang;
    };

    // Initialize Language
    const savedLang = localStorage.getItem('giko_lang') || 'ja';
    updateContent(savedLang);

    // Event Listeners
    const toggleLang = () => {
        const current = localStorage.getItem('giko_lang') || 'ja';
        const next = current === 'ja' ? 'en' : 'ja';
        updateContent(next);
    };

    const desktopToggle = document.getElementById('lang-toggle-desktop');
    const mobileToggle = document.getElementById('lang-toggle-mobile');

    if (desktopToggle) desktopToggle.addEventListener('click', toggleLang);
    if (mobileToggle) mobileToggle.addEventListener('click', toggleLang);

    // --- End Language Switcher ---
    // FAQ Accordion
    document.querySelectorAll('.faq-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            const icon = button.querySelector('svg');
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });

    // Mobile Menu
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    const toggleMenu = () => {
        mobileMenu.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    };

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', toggleMenu);
    }

    // Works Filter Logic (for Index Page)
    const workFilterContainer = document.getElementById('works-filter');
    if (workFilterContainer) {
        const filterBtns = workFilterContainer.querySelectorAll('.filter-btn');
        const workItems = document.querySelectorAll('.work-item');

        filterBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons
                filterBtns.forEach((b) => {
                    b.classList.remove(
                        'active',
                        'bg-primary',
                        'text-black',
                        'shadow-lg'
                    );
                    b.classList.add(
                        'bg-transparent',
                        'text-gray-400',
                        'hover:text-white'
                    );
                });
                // Add active class to clicked button
                btn.classList.add(
                    'active',
                    'bg-primary',
                    'text-black',
                    'shadow-lg'
                );
                btn.classList.remove(
                    'bg-transparent',
                    'text-gray-400',
                    'hover:text-white'
                );

                const filterValue = btn.getAttribute('data-filter');

                workItems.forEach((item) => {
                    if (
                        filterValue === 'all' ||
                        item.getAttribute('data-category').includes(filterValue)
                    ) {
                        item.style.display = 'block';
                        // Reset animation
                        item.classList.remove('visible');
                        requestAnimationFrame(() => {
                            // Use a timeout to ensure browser reflow registers the removal
                            setTimeout(() => item.classList.add('visible'), 10);
                        });
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            if (mobileMenu) mobileMenu.classList.add('hidden'); // Close menu on click
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            // Handle links like index.html#section from same page
            const hash = targetId.includes('#')
                ? targetId.split('#')[1]
                : targetId;
            const targetElement = document.getElementById(hash);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            } else if (!targetId.startsWith('#')) {
                // If it's a link to another page (though this selector mainly targets #links)
                // Let default happen if it's not a pure hash
                window.location.href = targetId;
            }
        });
    });

    // Intersection Observer for Fade In
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach((el) => observer.observe(el));

    // Scroll to Top Logic
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    if (scrollToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                scrollToTopBtn.classList.remove('translate-y-20', 'opacity-0');
            } else {
                scrollToTopBtn.classList.add('translate-y-20', 'opacity-0');
            }
        });
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Comparison Slider Logic
    const initSlider = () => {
        const container = document.getElementById('slider-container');
        const afterWrapper = document.getElementById('after-wrapper');
        const handle = document.getElementById('slider-handle');

        if (container && afterWrapper && handle) {
            let active = false;

            const move = (e) => {
                const rect = container.getBoundingClientRect();
                const x = (e.clientX || e.touches[0].clientX) - rect.left;
                const width = rect.width;

                // Clamp 0 to width
                const xClamped = Math.max(0, Math.min(x, width));
                const percent = (xClamped / width) * 100;

                afterWrapper.style.width = percent + '%';
                handle.style.left = percent + '%';
            };

            container.addEventListener('mousedown', () => (active = true));
            container.addEventListener('touchstart', () => (active = true));

            window.addEventListener('mouseup', () => (active = false));
            window.addEventListener('touchend', () => (active = false));

            container.addEventListener('mousemove', (e) => {
                if (!active) return;
                move(e);
            });
            container.addEventListener('touchmove', (e) => {
                if (!active) return;
                move(e);
            });

            // Hover follow (Optional enhancement)
            container.addEventListener('mousemove', (e) => {
                // If you want it to just follow mouse without clicking, uncomment
                // move(e);
            });
        }
    };

    initSlider();

    // Gallery Tabs Logic (Removed as per request)
    // All items remain visible by default
});
