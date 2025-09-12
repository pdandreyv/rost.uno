/**
 * RostPack Package JavaScript
 * Основная функциональность пакета
 */

class RostPack {
    constructor() {
        this.version = '1.0.0';
        this.initialized = false;
        this.init();
    }

    /**
     * Инициализация пакета
     */
    init() {
        if (this.initialized) return;
        
        console.log('🚀 RostPack инициализирован версии', this.version);
        
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
        this.setupVersionDisplay();
    }

    /**
     * Настройка обработчиков событий
     */
    setupEventListeners() {
        // Обработчик для клика по логотипу
        const logo = document.querySelector('.logo');
        if (logo) {
            logo.addEventListener('click', () => this.onLogoClick());
        }

        // Обработчик для версии
        const version = document.querySelector('.version');
        if (version) {
            version.addEventListener('click', () => this.showVersionInfo());
        }
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
     * Настройка отображения версии
     */
    setupVersionDisplay() {
        const versionElement = document.querySelector('.version');
        if (versionElement) {
            versionElement.title = 'Кликните для получения информации о версии';
            versionElement.style.cursor = 'pointer';
        }
    }

    /**
     * Обработчик клика по логотипу
     */
    onLogoClick() {
        console.log('Логотип RostPack нажат!');
        
        // Добавляем эффект пульсации
        const logo = document.querySelector('.logo');
        logo.style.transform = 'scale(1.1)';
        logo.style.transition = 'transform 0.2s ease';
        
        setTimeout(() => {
            logo.style.transform = 'scale(1)';
        }, 200);
    }

    /**
     * Показать информацию о версии
     */
    showVersionInfo() {
        const info = `
RostPack Package v${this.version}
Создано командой RostPack
Laravel Package Framework
        `.trim();
        
        alert(info);
    }

    /**
     * Получить информацию о пакете
     */
    getPackageInfo() {
        return {
            name: 'RostPack',
            version: this.version,
            description: 'Laravel Package Framework',
            author: 'RostPack Team'
        };
    }
}

// Инициализация при загрузке
window.RostPack = new RostPack();

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RostPack;
}
