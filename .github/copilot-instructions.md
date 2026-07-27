# Project Context

This is a php project using laravel with eloquent.

The API has 696 routes. See .codesight/routes.md for the full route map with methods, paths, and tags.
The database has 46 models. See .codesight/schema.md for the full schema with fields, types, and relations.
Middleware includes: auth, cors, custom.

High-impact files (most imported, changes here affect many other files):
- resources\js\absensi\map.js (imported by 1 files)
- resources\js\absensi\init.js (imported by 1 files)
- resources\js\absensi\camera.js (imported by 1 files)
- resources\js\absensi\time.js (imported by 1 files)

Required environment variables (no defaults):
- AWS_ACCESS_KEY_ID (.env)
- AWS_BUCKET (.env)
- AWS_SECRET_ACCESS_KEY (.env)
- DB_PASSWORD (.env)
- DB2_PASSWORD (.env)
- MAIL_PASSWORD (.env)

See .codesight/cicd.md for additional cicd context.

Read .codesight/wiki/index.md for orientation (WHERE things live). Then read actual source files before implementing. Wiki articles are navigation aids, not implementation guides.
Read .codesight/CODESIGHT.md for the complete AI context map including all routes, schema, components, libraries, config, middleware, and dependency graph.
