/**
 * RostPack Package JavaScript
 * Основная функциональность пакета
 * 
 * ВАЖНО: ВСЕ JavaScript должен быть в этом файле!
 * НЕ добавлять inline скрипты в Blade-шаблоны!
 * Только конфигурационный код (window.rostpackConfig) может быть в Blade.
 */

class RostPack {
    constructor() {
        this.version = '1.0.0';
        this.initialized = false;
        this.debug = false; // Включаем дебаг по умолчанию
        this.debugContainer = null;
        this.init();
    }

    /**
     * Инициализация пакета
     */
    init() {
        if (this.initialized) return;
        
        console.log('🚀 RostPack инициализирован версии', this.version);
        
        // Проверяем, запущено ли приложение в Telegram WebApp
        this.checkTelegramWebApp();
        
        // Инициализация после загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }
        
        this.initialized = true;
    }

    /**
     * Выполняется после загрузки DOM
     */
    onDOMReady() {
        this.setupEventListeners();
        this.animateElements();
        this.setupTelegramWebApp();
        this.setupDebugContainer();
    }

    /**
     * Настройка обработчиков событий
     */
    setupEventListeners() {       
        // Обработчик для меню профиля на странице профиля
        this.setupProfileMenu();
    }

    /**
     * Анимация элементов
     */
    animateElements() {
        const features = document.querySelectorAll('.features li');
        features.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, 200 + (index * 100));
        });
    }

    /**
     * Настройка меню профиля на странице профиля
     */
    setupProfileMenu() {
        // Закрыть меню при клике вне его
        document.addEventListener('click', (e) => {
            const profileContainer = document.querySelector('.profile-container');
            const menu = document.getElementById('profile-menu');
            
            if (profileContainer && menu && !profileContainer.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
    }

    /**
     * Переключение меню профиля
     */
    toggleProfileMenu() {
        const menu = document.getElementById('profile-menu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }

    /**
     * Обработка авторизации через Telegram
     */
    handleTelegramAuth(user) {
        this.log('Telegram auth data:', user);
        
        // Показываем индикатор загрузки
        const widgets = document.querySelectorAll('.telegram-login-widget');
        widgets.forEach(widget => {
            widget.innerHTML = '<div class="text-center"><div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div><p class="mt-1 text-xs text-gray-600">Авторизация...</p></div>';
        });
        
        // Отправляем данные пользователя на сервер
        const authUrl = window.rostpackConfig?.authUrl || '/rostpack/auth/telegram';
        this.log('Sending auth request to:', authUrl);
        this.log('User data:', user);
        
        fetch(authUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(user)
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                // Пытаемся получить текст ошибки
                return response.text().then(text => {
                    this.log('Error response:', text);
                    throw new Error(`Ошибка авторизации (${response.status}): ${text}`);
                });
            }
        })
        .then(data => {
            this.log('Server response:', data);
            
            if (data.success) {
                window.location.href = window.rostpackConfig?.indexUrl || '/rostpack/';
            } else {
                throw new Error(data.message || 'Ошибка авторизации');
            }
        })
        .catch(error => {
            this.log('Auth error:', error);
            this.restoreTelegramWidgets();
            alert('Ошибка авторизации: ' + error.message);
        });
    }

    /**
     * Восстановление Telegram виджетов
     */
    restoreTelegramWidgets() {
        this.log('🔄 Восстановление Telegram виджетов');
        const widgets = document.querySelectorAll('.telegram-login-widget');
        widgets.forEach(widget => {
            const botUsername = window.rostpackConfig?.botUsername;
            if (botUsername) {
                widget.innerHTML = `<script async src="https://telegram.org/js/telegram-widget.js?22.2" data-telegram-login="${botUsername}" data-size="medium" data-onauth="onTelegramAuth(user)" data-request-access="write"></script>`;
            } else {
                widget.innerHTML = '<button onclick="alert(\'Для авторизации через Telegram необходимо настроить bot_username в конфигурации пакета.\')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">Войти через Telegram</button>';
            }
        });
    }

    /**
     * Проверить, запущено ли приложение в Telegram WebApp
     */
    checkTelegramWebApp() {
        this.log('🔍 Проверка Telegram WebApp');
        
        // Логируем все доступные данные для отладки
        this.log('🔍 Отладочная информация:', {
            'window.rostpackConfig': window.rostpackConfig,
            'isDevelopmentMode': window.rostpackConfig?.isDevelopmentMode || false,
            'window.Telegram': typeof window.Telegram !== 'undefined' ? 'доступен' : 'недоступен',
            'window.Telegram.WebApp': typeof window.Telegram !== 'undefined' && window.Telegram.WebApp ? 'доступен' : 'недоступен',
            'navigator.userAgent': navigator.userAgent,
            'window.location.href': window.location.href,
            'document.referrer': document.referrer,
            'window.location.search': window.location.search,
        });
        
        // Используем данные из PHP конфигурации
        if (window.rostpackConfig && window.rostpackConfig.isTelegramWebApp) {
            this.isWebApp = true;
            this.log('📱 Telegram WebApp обнаружен (данные из PHP)');
            
            // В режиме разработки имитируем Telegram WebApp объект
            if (window.rostpackConfig && window.rostpackConfig.isDevelopmentMode) {
                this.log('🔧 Режим разработки: имитируем Telegram WebApp объект');
                this.telegramWebApp = this.createMockTelegramWebApp();
                this.log('📱 Mock Telegram WebApp объект создан');
                
                // Настраиваем WebApp
                this.setupTelegramWebApp();
            } else if (typeof window.Telegram !== 'undefined' && window.Telegram.WebApp) {
                this.telegramWebApp = window.Telegram.WebApp;
                this.log('📱 Telegram WebApp объект доступен');
                this.log('📱 WebApp данные:', {
                    'initData': this.telegramWebApp.initData,
                    'initDataUnsafe': this.telegramWebApp.initDataUnsafe,
                    'version': this.telegramWebApp.version,
                    'platform': this.telegramWebApp.platform,
                    'colorScheme': this.telegramWebApp.colorScheme,
                    'themeParams': this.telegramWebApp.themeParams,
                });
                
                // Настраиваем WebApp
                this.setupTelegramWebApp();
            } else {
                this.log('⚠️ Telegram WebApp объект недоступен, но PHP определил WebApp');
            }
        } else {
            this.isWebApp = false;
            this.log('🌐 Обычный веб-браузер (данные из PHP)');
            
            // Дополнительная проверка на клиентской стороне
            if (typeof window.Telegram !== 'undefined' && window.Telegram.WebApp) {
                this.log('⚠️ Telegram WebApp объект доступен, но PHP не определил WebApp');
                this.log('⚠️ Возможно, проблема с определением WebApp на сервере');
            }
        }
    }

    /**
     * Создать mock Telegram WebApp объект для разработки
     */
    createMockTelegramWebApp() {
        this.log('🔧 Создание mock Telegram WebApp объекта');
        
        return {
            initData: 'user=%7B%22id%22%3A123456789%2C%22first_name%22%3A%22Test%22%2C%22last_name%22%3A%22User%22%2C%22username%22%3A%22testuser%22%2C%22language_code%22%3A%22ru%22%2C%22is_premium%22%3Atrue%2C%22allows_write_to_pm%22%3Atrue%7D&chat_instance=-123456789&chat_type=private&auth_date=1234567890&hash=mock_hash',
            initDataUnsafe: {
                user: {
                    id: 123456789,
                    first_name: 'Test',
                    last_name: 'User',
                    username: 'testuser',
                    language_code: 'ru',
                    is_premium: true,
                    allows_write_to_pm: true,
                    photo_url: 'https://via.placeholder.com/150'
                },
                chat_instance: '-123456789',
                chat_type: 'private',
                auth_date: 1234567890
            },
            version: '6.0',
            platform: 'web',
            colorScheme: 'light',
            themeParams: {
                bg_color: '#ffffff',
                text_color: '#000000',
                hint_color: '#999999',
                link_color: '#2481cc',
                button_color: '#2481cc',
                button_text_color: '#ffffff'
            },
            isExpanded: false,
            viewportHeight: window.innerHeight,
            viewportStableHeight: window.innerHeight,
            headerColor: '#ffffff',
            backgroundColor: '#ffffff',
            isClosingConfirmationEnabled: false,
            isVerticalSwipesEnabled: true,
            isHorizontalSwipesEnabled: true,
            isSwipeGesturesEnabled: true,
            isHapticFeedbackEnabled: true,
            isClosingConfirmationEnabled: false,
            isVerticalSwipesEnabled: true,
            isHorizontalSwipesEnabled: true,
            isSwipeGesturesEnabled: true,
            isHapticFeedbackEnabled: true,
            isClosingConfirmationEnabled: false,
            isVerticalSwipesEnabled: true,
            isHorizontalSwipesEnabled: true,
            isSwipeGesturesEnabled: true,
            isHapticFeedbackEnabled: true,
            MainButton: {
                text: 'Continue',
                color: '#2481cc',
                textColor: '#ffffff',
                isVisible: false,
                isActive: true,
                isProgressVisible: false,
                setText: (text) => { this.log('MainButton.setText:', text); },
                onClick: (callback) => { this.log('MainButton.onClick registered'); },
                offClick: (callback) => { this.log('MainButton.offClick registered'); },
                show: () => { this.log('MainButton.show'); },
                hide: () => { this.log('MainButton.hide'); },
                enable: () => { this.log('MainButton.enable'); },
                disable: () => { this.log('MainButton.disable'); },
                showProgress: () => { this.log('MainButton.showProgress'); },
                hideProgress: () => { this.log('MainButton.hideProgress'); },
                setParams: (params) => { this.log('MainButton.setParams:', params); }
            },
            BackButton: {
                isVisible: false,
                onClick: (callback) => { this.log('BackButton.onClick registered'); },
                offClick: (callback) => { this.log('BackButton.offClick registered'); },
                show: () => { this.log('BackButton.show'); },
                hide: () => { this.log('BackButton.hide'); }
            },
            HapticFeedback: {
                impactOccurred: (style) => { this.log('HapticFeedback.impactOccurred:', style); },
                notificationOccurred: (type) => { this.log('HapticFeedback.notificationOccurred:', type); },
                selectionChanged: () => { this.log('HapticFeedback.selectionChanged'); }
            },
            CloudStorage: {
                setItem: (key, value, callback) => { this.log('CloudStorage.setItem:', key, value); if (callback) callback(true); },
                getItem: (key, callback) => { this.log('CloudStorage.getItem:', key); if (callback) callback('mock_value'); },
                getItems: (keys, callback) => { this.log('CloudStorage.getItems:', keys); if (callback) callback({}); },
                removeItem: (key, callback) => { this.log('CloudStorage.removeItem:', key); if (callback) callback(true); },
                removeItems: (keys, callback) => { this.log('CloudStorage.removeItems:', keys); if (callback) callback(true); },
                getKeys: (callback) => { this.log('CloudStorage.getKeys'); if (callback) callback([]); }
            },
            BiometricManager: {
                isInited: false,
                isBiometricAvailable: false,
                isAccessRequested: false,
                isAccessGranted: false,
                isBiometricTokenSaved: false,
                isBiometricTokenValid: false,
                biometricType: 'unknown',
                isAccessRequested: false,
                isAccessGranted: false,
                isBiometricTokenSaved: false,
                isBiometricTokenValid: false,
                biometricType: 'unknown',
                init: (callback) => { this.log('BiometricManager.init'); if (callback) callback(); },
                requestAccess: (callback) => { this.log('BiometricManager.requestAccess'); if (callback) callback(); },
                authenticate: (callback) => { this.log('BiometricManager.authenticate'); if (callback) callback(); },
                updateBiometricToken: (token, callback) => { this.log('BiometricManager.updateBiometricToken'); if (callback) callback(); },
                openSettings: () => { this.log('BiometricManager.openSettings'); },
                close: () => { this.log('BiometricManager.close'); }
            },
            ready: () => { this.log('WebApp.ready called'); },
            expand: () => { this.log('WebApp.expand called'); },
            close: () => { this.log('WebApp.close called'); },
            sendData: (data) => { this.log('WebApp.sendData:', data); },
            switchInlineQuery: (query, choose_chat_types) => { this.log('WebApp.switchInlineQuery:', query, choose_chat_types); },
            openLink: (url, options) => { this.log('WebApp.openLink:', url, options); window.open(url, '_blank'); },
            openTelegramLink: (url) => { this.log('WebApp.openTelegramLink:', url); },
            openInvoice: (url, callback) => { this.log('WebApp.openInvoice:', url); if (callback) callback('paid'); },
            showPopup: (params, callback) => { this.log('WebApp.showPopup:', params); if (callback) callback('ok'); },
            showAlert: (message, callback) => { this.log('WebApp.showAlert:', message); alert(message); if (callback) callback(); },
            showConfirm: (message, callback) => { this.log('WebApp.showConfirm:', message); const result = confirm(message); if (callback) callback(result); },
            showScanQrPopup: (params, callback) => { this.log('WebApp.showScanQrPopup:', params); if (callback) callback('text'); },
            closeScanQrPopup: () => { this.log('WebApp.closeScanQrPopup'); },
            readTextFromClipboard: (callback) => { this.log('WebApp.readTextFromClipboard'); if (callback) callback('mock_clipboard_text'); },
            requestWriteAccess: (callback) => { this.log('WebApp.requestWriteAccess'); if (callback) callback(true); },
            requestContact: (callback) => { this.log('WebApp.requestContact'); if (callback) callback({ contact: { phone_number: '+1234567890', first_name: 'Test', last_name: 'User' } }); },
            onEvent: (eventType, eventHandler) => { this.log('WebApp.onEvent:', eventType); },
            offEvent: (eventType, eventHandler) => { this.log('WebApp.offEvent:', eventType); },
            sendData: (data) => { this.log('WebApp.sendData:', data); },
            switchInlineQuery: (query, choose_chat_types) => { this.log('WebApp.switchInlineQuery:', query, choose_chat_types); },
            openLink: (url, options) => { this.log('WebApp.openLink:', url, options); window.open(url, '_blank'); },
            openTelegramLink: (url) => { this.log('WebApp.openTelegramLink:', url); },
            openInvoice: (url, callback) => { this.log('WebApp.openInvoice:', url); if (callback) callback('paid'); },
            showPopup: (params, callback) => { this.log('WebApp.showPopup:', params); if (callback) callback('ok'); },
            showAlert: (message, callback) => { this.log('WebApp.showAlert:', message); alert(message); if (callback) callback(); },
            showConfirm: (message, callback) => { this.log('WebApp.showConfirm:', message); const result = confirm(message); if (callback) callback(result); },
            showScanQrPopup: (params, callback) => { this.log('WebApp.showScanQrPopup:', params); if (callback) callback('text'); },
            closeScanQrPopup: () => { this.log('WebApp.closeScanQrPopup'); },
            readTextFromClipboard: (callback) => { this.log('WebApp.readTextFromClipboard'); if (callback) callback('mock_clipboard_text'); },
            requestWriteAccess: (callback) => { this.log('WebApp.requestWriteAccess'); if (callback) callback(true); },
            requestContact: (callback) => { this.log('WebApp.requestContact'); if (callback) callback({ contact: { phone_number: '+1234567890', first_name: 'Test', last_name: 'User' } }); },
            onEvent: (eventType, eventHandler) => { this.log('WebApp.onEvent:', eventType); },
            offEvent: (eventType, eventHandler) => { this.log('WebApp.offEvent:', eventType); }
        };
    }

    /**
     * Настройка Telegram WebApp
     */
    setupTelegramWebApp() {
        if (!this.isWebApp || !this.telegramWebApp) {
            this.log('❌ Telegram WebApp недоступен, пропускаем настройку');
            return;
        }

        this.log('🔧 Настройка Telegram WebApp...');

        // Расширяем приложение на весь экран
        this.telegramWebApp.expand();
        this.log('📱 WebApp расширен на весь экран');

        // Настраиваем тему
        this.telegramWebApp.ready();
        this.log('🎨 WebApp готов к работе');

        // Устанавливаем обработчики событий
        this.telegramWebApp.onEvent('viewportChanged', () => {
            this.log('Viewport changed');
        });

        // Показываем главную кнопку, если нужно
        if (this.telegramWebApp.MainButton) {
            this.telegramWebApp.MainButton.hide();
            this.log('🔘 Главная кнопка скрыта');
        }

        // Показываем кнопку "Назад", если нужно
        if (this.telegramWebApp.BackButton) {
            this.telegramWebApp.BackButton.hide();
            this.log('⬅️ Кнопка "Назад" скрыта');
        }

        // Применяем тему Telegram
        this.applyTelegramTheme();

        // Пытаемся автоматически авторизовать пользователя
        this.attemptAutoAuth();
    }

    /**
     * Применить тему Telegram
     */
    applyTelegramTheme() {
        if (!this.telegramWebApp) {
            this.log('❌ Telegram WebApp недоступен, пропускаем применение темы');
            return;
        }

        const theme = this.telegramWebApp.themeParams;
        this.log('🎨 Применение темы Telegram:', theme);
        
        // Применяем цвета темы
        if (theme.bg_color) {
            document.body.style.backgroundColor = theme.bg_color;
            this.log('🎨 Фон установлен:', theme.bg_color);
        }
        
        if (theme.text_color) {
            document.body.style.color = theme.text_color;
            this.log('🎨 Цвет текста установлен:', theme.text_color);
        }

        // Применяем CSS переменные для более детальной настройки
        const root = document.documentElement;
        if (theme.bg_color) root.style.setProperty('--tg-bg-color', theme.bg_color);
        if (theme.text_color) root.style.setProperty('--tg-text-color', theme.text_color);
        if (theme.hint_color) root.style.setProperty('--tg-hint-color', theme.hint_color);
        if (theme.link_color) root.style.setProperty('--tg-link-color', theme.link_color);
        if (theme.button_color) root.style.setProperty('--tg-button-color', theme.button_color);
        if (theme.button_text_color) root.style.setProperty('--tg-button-text-color', theme.button_text_color);
    }

    /**
     * Попытка автоматической авторизации
     */
    attemptAutoAuth() {
        if (!this.telegramWebApp) {
            this.log('❌ Telegram WebApp недоступен, пропускаем авторизацию');
            return;
        }

        this.log('🔐 Попытка автоматической авторизации');
        // Проверяем, не авторизован ли уже пользователь
        if (this.isUserAuthenticated()) {
            this.log('✅ Пользователь уже авторизован, пропускаем авторизацию');
            return;
        }

        // Получаем данные пользователя из WebApp
        const user = this.telegramWebApp.initDataUnsafe?.user;
        
        if (user) {
            this.log('👤 Данные пользователя получены:', user);
            
            // Отправляем данные на сервер для авторизации
            this.sendWebAppAuthData(user);
        } else {
            this.log('❌ Данные пользователя недоступны');
        }
    }

    /**
     * Отправить данные авторизации WebApp на сервер
     */
    sendWebAppAuthData(user) {
        this.log('📤 Отправка данных авторизации WebApp на сервер');
        const authUrl = window.rostpackConfig?.webappAuthUrl || '/rostpack/auth/webapp';
        
        this.log('📤 URL авторизации:', authUrl);
        this.log('👤 Данные пользователя:', user);

        // Показываем индикатор загрузки
        this.showLoadingIndicator();

        fetch(authUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Telegram-WebApp-Data': this.telegramWebApp.initData
            },
            body: JSON.stringify(user)
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    this.log('❌ Ошибка ответа сервера:', text);
                    throw new Error(`Ошибка авторизации (${response.status}): ${text}`);
                });
            }
        })
        .then(data => {
            this.log('✅ Ответ сервера:', data);
            
            if (data.success) {
                this.log('🎉 Авторизация успешна!');
                this.hideLoadingIndicator();
                
                // НЕ обновляем страницу, просто скрываем индикатор загрузки
                // Пользователь уже авторизован и контент должен обновиться автоматически
                this.log('✅ Авторизация успешна, пользователь авторизован');
                
                // Можно добавить уведомление об успешной авторизации
                if (this.telegramWebApp && this.telegramWebApp.showAlert) {
                    this.telegramWebApp.showAlert('Авторизация успешна!');
                }
            } else {
                throw new Error(data.message || 'Ошибка авторизации');
            }
        })
        .catch(error => {
            this.log('❌ Ошибка авторизации:', error);
            this.hideLoadingIndicator();
            // Не показываем alert в WebApp, так как это может быть неудобно
            if (!this.isWebApp) {
                alert('Ошибка авторизации: ' + error.message);
            }
        });
    }

    /**
     * Показать индикатор загрузки
     */
    showLoadingIndicator() {
        // Создаем индикатор загрузки
        const loader = document.createElement('div');
        loader.id = 'telegram-webapp-loader';
        loader.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
                <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                    <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
                    <p>Авторизация через Telegram...</p>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        document.body.appendChild(loader);
    }

    /**
     * Скрыть индикатор загрузки
     */
    hideLoadingIndicator() {
        const loader = document.getElementById('telegram-webapp-loader');
        if (loader) {
            loader.remove();
        }
    }

    /**
     * Проверить, авторизован ли пользователь
     */
    isUserAuthenticated() {
        this.log('🔍 Проверка статуса авторизации пользователя');
        
        // Проверяем наличие элементов, указывающих на авторизацию
        const authElements = document.querySelectorAll('a[href*="profile"]');
        const hasAuthElements = authElements.length > 0;
        
        // Проверяем наличие кнопок авторизации (их не должно быть, если пользователь авторизован)
        const loginButtons = document.querySelectorAll('.telegram-login-widget');
        const hasLoginButtons = loginButtons.length > 0;
        
        // Проверяем URL - если мы на главной странице, значит авторизованы
        const isOnIndex = window.location.pathname === '/rostpack' || window.location.pathname === '/rostpack/';
        
        this.log('Auth check:', {
            hasAuthElements,
            hasLoginButtons,
            isOnIndex,
            pathname: window.location.pathname
        });
        
        const isAuthenticated = hasAuthElements || isOnIndex;
        this.log(`🔐 Результат проверки авторизации: ${isAuthenticated ? 'авторизован' : 'не авторизован'}`);
        
        return isAuthenticated;
    }

    /**
     * Настройка контейнера для дебага
     */
    setupDebugContainer() {
        if (!this.debug) {
            return;
        }

        this.log('🔧 Создание контейнера для дебага');
        // Создаем контейнер для дебага
        this.debugContainer = document.createElement('div');
        this.debugContainer.id = 'rostpack-debug-container';
        this.debugContainer.innerHTML = `
            <div style="position: fixed; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.9); color: #00ff00; font-family: monospace; font-size: 12px; padding: 10px; max-height: 200px; overflow-y: auto; z-index: 10000; border-top: 2px solid #00ff00;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <strong>🔧 RostPack Debug Console</strong>
                    <button onclick="window.RostPack.toggleDebug()" style="background: #333; color: #00ff00; border: 1px solid #00ff00; padding: 2px 8px; cursor: pointer; font-size: 10px;">Свернуть</button>
                </div>
                <div id="rostpack-debug-logs" style="white-space: pre-wrap; line-height: 1.4;"></div>
            </div>
        `;
        
        document.body.appendChild(this.debugContainer);
        this.log('🚀 RostPack Debug Console инициализирована');
        this.log('🔧 Debug mode включен, все логи будут отображаться внизу страницы');
        this.log('📱 Telegram WebApp:', this.isWebApp ? 'да' : 'нет');
        this.log('🌐 User Agent:', navigator.userAgent);
        this.log('📍 URL:', window.location.href);
    }

    /**
     * Переключить видимость дебаг консоли
     */
    toggleDebug() {
        if (!this.debugContainer) {
            this.log('❌ Debug контейнер не найден');
            return;
        }
        
        this.log('🔄 Переключение видимости debug консоли');
        const logs = this.debugContainer.querySelector('#rostpack-debug-logs');
        if (logs) {
            const isHidden = logs.style.display === 'none';
            logs.style.display = isHidden ? 'block' : 'none';
            this.log(`🔧 Debug console ${isHidden ? 'показана' : 'скрыта'}`);
        } else {
            this.log('❌ Элемент логов не найден');
        }
    }

    /**
     * Логирование в дебаг консоль
     */
    log(message, data = null) {
        if (!this.debug) return;
        
        const timestamp = new Date().toLocaleTimeString();
        let logMessage = `[${timestamp}] ${message}`;
        
        if (data) {
            logMessage += '\n' + JSON.stringify(data, null, 2);
        }
        
        console.log(logMessage); // Также выводим в обычную консоль
        
        if (this.debugContainer) {
            const logsContainer = this.debugContainer.querySelector('#rostpack-debug-logs');
            if (logsContainer) {
                const logEntry = document.createElement('div');
                logEntry.style.marginBottom = '2px';
                logEntry.style.borderBottom = '1px solid #333';
                logEntry.style.paddingBottom = '2px';
                logEntry.textContent = logMessage;
                
                logsContainer.appendChild(logEntry);
                
                // Прокручиваем вниз
                logsContainer.scrollTop = logsContainer.scrollHeight;
                
                // Ограничиваем количество записей (последние 50)
                const entries = logsContainer.children;
                if (entries.length > 50) {
                    logsContainer.removeChild(entries[0]);
                }
            }
        }
    }

    /**
     * Очистить дебаг логи
     */
    clearDebug() {
        if (this.debugContainer) {
            this.log('🧹 Очистка debug консоли');
            const logsContainer = this.debugContainer.querySelector('#rostpack-debug-logs');
            if (logsContainer) {
                logsContainer.innerHTML = '';
                this.log('🧹 Debug console очищена');
            } else {
                this.log('❌ Контейнер логов не найден');
            }
        } else {
            this.log('❌ Debug контейнер не найден');
        }
    }
}

// Инициализация при загрузке
window.RostPack = new RostPack();

// Глобальная функция для Telegram виджета
window.onTelegramAuth = function(user) {
    if (window.RostPack && typeof window.RostPack.handleTelegramAuth === 'function') {
        window.RostPack.handleTelegramAuth(user);
    } else {
        console.error('RostPack not initialized or handleTelegramAuth method not found');
        alert('Ошибка инициализации. Попробуйте обновить страницу.');
    }
};

// Также делаем функцию доступной напрямую
window.handleTelegramAuth = function(user) {
    if (window.RostPack && typeof window.RostPack.handleTelegramAuth === 'function') {
        window.RostPack.handleTelegramAuth(user);
    } else {
        console.error('RostPack not initialized or handleTelegramAuth method not found');
        alert('Ошибка инициализации. Попробуйте обновить страницу.');
    }
};

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RostPack;
}
