import ColorVueController from './controllers/fields/color-vue';
import FileController from './controllers/fields/file';
import QuillController from './controllers/fields/quill';
import SlugController from './controllers/fields/slug';
import SortableController from './controllers/fields/sortable';
import UploadController from './controllers/fields/upload';

window.application.register('slug', SlugController);
window.application.register('color-vue', ColorVueController);
window.application.register('sortable', SortableController);
window.application.register('kelnik-quill', QuillController);
window.application.register('kelnik-file', FileController);
window.application.register('kelnik-upload', UploadController);
