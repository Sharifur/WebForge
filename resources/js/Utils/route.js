// Simple route helper for when Ziggy is not available
export const route = (name, params = {}) => {
  const routes = {
    'admin.pages.index': '/admin/pages',
    'admin.pages.edit': (id) => `/admin/pages/${id}/edit`,
    'admin.pages.update': (id) => `/admin/pages/${id}`,
    'page.show': (slug) => `/page/${slug}`,
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