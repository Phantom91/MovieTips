const API_ENDPOINT = 'http://localhost:9080/movietips/api',
    _DefaultLanguage = 'en',
    _RenderService = `${API_ENDPOINT}/render`,
    _TemplatesPath = 'client/webapp/templates';

const AppConfig = {  
    get API_ENDPOINT() {
        return API_ENDPOINT;
    },
    get DefaultLanguage() {
        return _DefaultLanguage;
    },
    get RenderService() {
        return _RenderService;
    },
    get TemplatesPath() {
        return _TemplatesPath;
    }
};

export default AppConfig;