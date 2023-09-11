import type {IYandexMapCluster} from './types';
import type ymaps from 'yandex-maps';

class Cluster implements IYandexMapCluster {
    private ymaps: typeof ymaps = window.ymaps;
    private geoObjectCollection: ymaps.GeoObjectCollection;
    private clusterer: ymaps.Clusterer;

    /**
     * Метод инициализирует модуль
     * @param {object} geoObjectCollection - коллекция с гео-объектами
     * @public
     */
    public init(geoObjectCollection: ymaps.GeoObjectCollection): void {
        this.geoObjectCollection = geoObjectCollection;

        this._create();
    }

    /**
     * Добавляет объекты на кластер карты
     * @param {Object} geoObject - геообъект карты для добавления в кластер
     * @public
     */
    public addGeoObjectToCluster(geoObject: ymaps.IGeoObject): void {
        this.clusterer.add(geoObject);
    }

    /**
     * Удаляет объекты из кластера карты
     * @param {Object} geoObject - геообъект карты для удаления из кластера
     * @public
     */
    public removeGeoObjectToCluster(geoObject: ymaps.IGeoObject): void {
        this.clusterer.remove(geoObject);
    }

    /**
     * Метод создает кластеры
     * @private
     */
    private _create(): void {
        // не понятно как в твиг передать properties
        const CustomLayoutClass: ymaps.IClassConstructor<ymaps.layout.templateBased.Base> =
            this.ymaps.templateLayoutFactory
                .createClass('<div class="yandex-map__cluster">{{ properties.geoObjects.length }}</div>');

        const lat = 0;
        const lon = 0;

        this.clusterer = new this.ymaps.Clusterer({
            // @ts-ignore
            clusterIconLayout : CustomLayoutClass,
            groupByCoordinates: false,
            gridSize          : 100,
            zoomMargin        : 50,
            hasBalloon        : false,
            clusterIconShape  : {
                type       : 'Circle',
                coordinates: [lat, lon],
                radius     : 23
            }
        });

        // фильтруем массив только с маркерами + только те у которых нет запрета на добавления в кластеры
        const markers = this.geoObjectCollection.toArray().filter((geoObject: ymaps.IGeoObject) => {
            const isMarker = geoObject.options.getNative('type').toString() === 'Placemark';

            // @ts-ignore
            return isMarker && !geoObject.options.getNative('data').not_cluster;
        });

        this.clusterer.add(markers);

        // @ts-ignore
        this.geoObjectCollection.add(this.clusterer);
    }
}

export default Cluster;
