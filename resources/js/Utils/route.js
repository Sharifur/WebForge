import { Ziggy } from '../ziggy';

// Route helper that uses Ziggy
export const route = (name, params = {}) => {
  // Try to use window.route (Ziggy) if available
  if (typeof window !== 'undefined' && window.route && typeof window.route === 'function') {
    try {
      return window.route(name, params);
    } catch (e) {
      console.error('Ziggy route error:', e);
      // Fall back to manual routes if Ziggy fails
    }
  }
  
  // Manual route generation using Ziggy config
  if (Ziggy && Ziggy.routes && Ziggy.routes[name]) {
    const routeConfig = Ziggy.routes[name];
    let uri = routeConfig.uri;
    
    // Replace parameters in URI
    if (routeConfig.parameters && params) {
      routeConfig.parameters.forEach(param => {
        if (params.hasOwnProperty(param) || typeof params === 'string' || typeof params === 'number') {
          const value = typeof params === 'object' ? params[param] : params;
          uri = uri.replace(`{${param}}`, value);
        }
      });
    }
    
    const baseUrl = Ziggy.url || '';
    return `${baseUrl}/${uri}`.replace(/\/+/g, '/').replace(/\/$/, '') || '/';
  }
  
  const routes = {
    'admin.pages.index': '/admin/pages',
    'admin.pages.edit': (id) => `/admin/pages/${id}/edit`,
    'admin.pages.update': (id) => `/admin/pages/${id}`,
    'page.show': (slug) => `/${slug}`,
  };

  const routeTemplate = routes[name];
  
  if (typeof routeTemplate === 'function') {
    return routeTemplate(params);
  }
  
  if (typeof routeTemplate === 'string') {
    return routeTemplate;
  }
  
  return '#';
};

// Make it available globally if window.route doesn't exist
if (typeof window !== 'undefined' && !window.route) {
  window.route = route;
}