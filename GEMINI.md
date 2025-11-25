# Gemini Integration Guide  
### Laravel + React Page Builder (PHP-First Architecture)

This document explains how Gemini is used inside this project, how to set it up locally, and how contributors can run Gemini-powered developer tools, code generation, and content assistance.  

Gemini is *not required* to run the page builder runtime, but it enhances:  
- Component schema generation  
- Block metadata suggestions  
- Layout recommendations  
- JSX/Blade template generation  
- Developer productivity (CLI prompts)

---

## 1. Purpose of Gemini in This Project

Gemini is used as an **AI assistant for development**, not as part of the runtime.  
It is used for:

- Generating new **React components** for Page Builder blocks  
- Generating **Blade** or **JSON block definitions**  
- Suggesting **layout structures**  
- Producing **component config schemas**  
- Helping write reusable **PHP builder classes**  
- Automating repetitive boilerplate (actions, reducers, hooks, tests, etc.)

**No Gemini API calls occur in production.**  
It is strictly a **dev-only automation tool**.
