import type ymaps from 'yandex-maps';

export interface IYMapsOuterSettings extends Partial<IYMapsSettings> {
    wrapper: HTMLElement;
    cluster: boolean;
    fullScreenControl: boolean;
    customFullScreenControl: boolean;
    customZoomControl: boolean;
}

export interface IYandexMapBalloonData {
    title: string | null;
    image: string | null;
    text: string | null;
}

export interface IYandexMapPolygonData {
    id: number;
    coords: number[][];
    image: string;
}

export interface IYandexMapMarkerData {
    id: number;
    coords: number[];
    icon: {
        src?: string;
        size: {
            width: number;
            height: number;
        };
    };
    type?: string;
    modifyClass?: string;
    balloon?: IYandexMapBalloonData;
    isHidden?: boolean;
    zIndex?: number;
    not_cluster?: boolean;
}

export interface IYMapsSettings extends ymaps.IMapOptions, ymaps.IMapState {
    center?: number[];
    autoZoom?: boolean;
    zoom: number;
    zoomMargin?: number | number[] | number[][];
    minZoom: number;
    maxZoom: number;
    zoomStep?: number;
    controls?: any[];
    zoomControl?: boolean;
    customZoomControl?: boolean;
    fullScreenControl?: boolean;
    customFullScreenControl?: boolean;
    disableMobileDrag?: boolean;
    height?: number;
    zoomScroll?: boolean;
    markers: IYandexMapMarkerData[];
    polygons?: IYandexMapPolygonData[];
}

export enum EYandexMapPanelState {
    close = 'close',
    open = 'show'
}

export interface IYandexMapControls {
    init(map: ymaps.Map, settings: IYMapsSettings, ymap: typeof ymaps): void;
}

export interface IYandexMapPanel {
    open(target: ymaps.Placemark): void;
}

export interface IYandexMapMarker {
    createHtmlMarker(element: IYandexMapMarkerData): ymaps.Placemark;
    closeBalloonOnClickMap(map: ymaps.Map): void;
}

export interface IYandexMapCluster {
    init(geoObjectCollection: ymaps.GeoObjectCollection): void;
    addGeoObjectToCluster(geoObject: ymaps.IGeoObject): void;
    removeGeoObjectToCluster(geoObject: ymaps.IGeoObject): void;
}

export interface IYandexMapFilterSettings {
    target: HTMLElement;
}

export interface IYandexMapFilter {
    globalFilterState: boolean;
    init(settings: IYandexMapFilterSettings): void;
    changeCheckBoxState(): void;
}

export interface IYandexMap {
    init(outerOptions: IYMapsOuterSettings): void;
}
