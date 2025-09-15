# Esquema de Base de Datos: ftoliboy_toliboy_data

Resumen
- Motor: MySQL (InnoDB), charset utf8mb4, collation utf8mb4_general_ci.
- Esquema: ftoliboy_toliboy_data.
- Auditoría completa mediante triggers a tabla audit_logs.
- Manejo de “sesión de BD por conexión” con funciones, procedimientos y un evento de limpieza.
- Amplio uso de claves foráneas, índices y restricciones únicas.
- Columnas JSON en varias tablas (flexibilidad para metadatos).

Índice de contenidos
- Tablas
- Funciones, Procedimientos y Evento
- Vistas
- Triggers de Auditoría
- Notas y Recomendaciones (alineación con la API Laravel)

Tablas (estructura y restricciones)
1) roles
- Campos: id, name (UNIQUE), description, permissions (JSON), is_active, timestamps.
- Índices: UNIQUE(name), idx_role_active.

2) users
- Campos: id, name, email (UNIQUE), password, role_id (FK→roles), position, is_active, last_login, timestamps.
- Índices: UNIQUE(email), fk_users_roles_idx, idx_user_active, idx_user_email.
- FK: role_id → roles(id) ON DELETE SET NULL ON UPDATE CASCADE.

3) raw_materials
- Campos: id, name, code (UNIQUE), description, unit_of_measure, min_stock, is_active, created_by(FK→users), timestamps.
- Índices: UNIQUE(code), idx_material_active.
- FK: created_by → users(id) ON DELETE SET NULL ON UPDATE CASCADE.

4) personal_data
- Campos: id, user_id (UNIQUE y FK→users), num_phone, num_phone_alt, num_identification (UNIQUE), identification_type, address, emergency_contact, emergency_phone, timestamps.
- Índices: UNIQUE(user_id), UNIQUE(num_identification), fk_personal_data_users_idx.
- FK: user_id → users(id) ON DELETE CASCADE ON UPDATE CASCADE.

5) audit_logs
- Campos: id, table_name, record_id, action (ENUM INSERT/UPDATE/DELETE), old_values (JSON), new_values (JSON), user_id (FK→users), ip_address, user_agent, created_at.
- Índices: varios para table_name, record, created_at, user y action.
- FK: user_id → users(id) ON DELETE SET NULL ON UPDATE CASCADE.

6) products
- Campos: id, name, code (UNIQUE), category, description, specifications (JSON), unit_price, is_active, created_by(FK→users), timestamps.
- Índices: UNIQUE(code), idx_product_category, idx_product_active, fk_products_creator_idx.
- FK: created_by → users(id) ON DELETE SET NULL ON UPDATE CASCADE.

7) batches
- Campos: id, name, code (UNIQUE), product_id (FK→products), start_date, expected_end_date, actual_end_date, status (ENUM planned/in_process/paused/completed/delivered/cancelled), quantity, defect_quantity, notes, created_by(FK→users), timestamps.
- Índices: UNIQUE(code), fk_batches_products_idx, idx_batch_status, idx_batch_dates, fk_batches_creator_idx.
- FK: product_id → products(id) SET NULL; created_by → users(id) SET NULL.

8) inventory_movements
- Campos: id, raw_material_id (FK→raw_materials), batch_id (FK→batches), movement_type (ENUM in/out/adjustment), quantity, unit_cost, notes, created_by(FK→users), created_at.
- Índices: idx_inventory_material, idx_inventory_batch, idx_inventory_type.
- FKs: raw_material_id → raw_materials(id) CASCADE; batch_id → batches(id) SET NULL; created_by → users(id) SET NULL.

9) current_user_sessions
- Campos: id (string, PK), user_id (FK→users), ip_address, user_agent, payload (texto de sesión), last_activity (int), connection_id (UNIQUE), created_at, expires_at.
- Índices: por user_id, ip_address, last_activity, expires_at.
- FK: user_id → users(id) ON DELETE CASCADE ON UPDATE CASCADE.
- Uso: correlaciona conexión MySQL con usuario/IP/UA y vence a 1 hora.

10) forms
- Campos: id, name, code, description, version, created_by(FK→users), is_active, display_order, timestamps.
- Índices: UNIQUE(code, version), fk_forms_users_idx, idx_form_active.
- FK: created_by → users(id) SET NULL.

11) form_fields
- Campos: id, form_id (FK→forms), label, field_code, type (ENUM text, number, date, time, select, checkbox, radio, textarea, file), required, options (JSON), validation_rules (JSON), field_order, is_active, timestamps.
- Índices: UNIQUE(form_id, field_code), fk_form_fields_forms_idx, idx_form_field_order, idx_field_active.
- FK: form_id → forms(id) CASCADE.

12) form_responses
- Campos: id, form_id(FK→forms), user_id(FK→users), batch_id(FK→batches), status (ENUM pending/in_progress/completed/approved/rejected), submitted_at, reviewed_by(FK→users), reviewed_at, review_notes, timestamps.
- Índices: por form_id, user_id, batch_id, status, created_at, reviewed_by.
- FKs: form_id → forms(id) CASCADE; user_id → users(id) CASCADE; batch_id → batches(id) SET NULL; reviewed_by → users(id) SET NULL.

13) form_response_values
- Campos: id, response_id(FK→form_responses), field_id(FK→form_fields), value, file_path, timestamps.
- Índices: UNIQUE(response_id, field_id), y varios para acceso por response/field.
- FKs: response_id → form_responses(id) CASCADE; field_id → form_fields(id) CASCADE.

14) work_logs
- Campos: id, user_id(FK→users), date, start_time (TIME), end_time (TIME), total_hours (GENERATED por TIMESTAMPDIFF(HOUR, date+start_time, date+end_time)), overtime_hours, batch_id(FK→batches), task_description, notes, timestamps.
- Índices: por user_id, batch_id, date, (user_id, date).
- FKs: user_id → users(id) CASCADE; batch_id → batches(id) SET NULL.

15) notifications
- Campos: id, user_id(FK→users), title, message, type (ENUM info/warning/error/success), is_read, related_table, related_id, expires_at, created_at.
- Índices: por user_id, is_read, type, (related_table, related_id).
- FK: user_id → users(id) CASCADE.

Funciones, Procedimientos y Evento
Funciones
- get_current_user_id(): retorna user_id activo según connection_id y no vencido (1h).
- get_current_ip_address(): retorna IP actual asociada a la conexión (o 0.0.0.0).
- get_current_user_agent(): retorna user-agent actual (o ‘Desconocido’).

Procedimientos
- set_current_user(p_user_id, p_ip_address, p_user_agent)
  - Limpia sesiones expiradas y la de la conexión actual.
  - Inserta nueva fila en current_user_sessions con vencimiento +1 hora.
- clean_expired_sessions()
  - Elimina sesiones vencidas.

Evento
- session_cleanup_event (cada hora): ejecuta clean_expired_sessions() automáticamente.
- Requiere EVENT_SCHEDULER=ON en MySQL.

Vistas (resumen y propósito)
Usuarios/Roles
- v_users_by_role: conteo de usuarios por rol y estado.

Productos
- v_products_by_category: agregados de productos por categoría y estado (avg/min/max precio).

Lotes (Batches)
- v_batches_by_status: conteo por estado.
- v_batches_by_product: resumen por producto y estado (cantidad total y defectos).
- v_batches_lead_times: días para completar y desviación vs. esperado (lotes completados).
- v_batches_defect_rate: tasa de defectos por lote.

Inventario
- v_current_stock: stock actual por material considerando entradas/salidas/ajustes.
- v_stock_below_min: materiales por debajo del stock mínimo (faltante).
- v_inventory_monthly_summary: resumen mensual in/out/adjustment y valor de entradas.

Formularios y Respuestas
- v_forms_status_summary: conteo de respuestas por estado y formulario.
- v_forms_completion_rate: tasa de finalización por formulario.
- v_form_field_usage: uso por campo (respuestas y archivos adjuntos).
- v_form_review_time_hours: horas entre submitted_at y reviewed_at.

Trabajo
- user_work_summary: resumen de días/hours por usuario.
- v_user_work_hours_by_month: horas por usuario y mes.

Sesiones
- active_sessions: sesiones activas con tiempo restante.

Notificaciones
- v_notifications_summary_by_user: resumen por usuario, tipo y leído/no leído.

Triggers de Auditoría
- Para cada tabla principal (roles, users, raw_materials, personal_data, products, batches, inventory_movements, forms, form_fields, form_responses, form_response_values, work_logs, notifications) existen AFTER INSERT/UPDATE/DELETE.
- Inserta en audit_logs:
  - table_name, record_id, action
  - old_values/new_values (JSON con snapshot relevante)
  - user_id/ip_address/user_agent mediante funciones get_current_*.
- Requiere que el procedimiento set_current_user sea llamado por conexión (middleware SetDbSessionUser ya implementado en Laravel).

Notas y Recomendaciones (alineación con la API Laravel)
- ENUMs inventario: movement_type es (‘in’, ‘out’, ‘adjustment’). En la validación Laravel se usa ‘addition’, ‘removal’, ‘adjustment’. Sugerencia: unificar al mismo set de valores (backend y BD).
- Formularios unique:
  - forms tiene UNIQUE (code, version). En FormRequest se valida unique solo por code. Ajustar validación para compuesta o manejar version en reglas.
  - form_fields tiene UNIQUE (form_id, field_code). En FormRequest se valida unique por field_code. Ajustar a regla compuesta (Rule::unique()->where('form_id', ...)).
  - form_response_values tiene UNIQUE (response_id, field_id). Validar combinación en store/update (ya se cubrió en el controlador).
- Work logs:
  - total_hours usa TIMESTAMPDIFF(HOUR...), precisión a horas enteras. Si se requiere precisión por minutos, usar TIMESTAMPDIFF(MINUTE)/60 o DECIMAL con cálculo más fino.
  - start_time/end_time son TIME. En FormRequest usar reglas time (date_format:H:i[:s]) en lugar de date.
- Sesiones en BD:
  - Asegurar EVENT_SCHEDULER=ON para que session_cleanup_event funcione.
  - Middleware debe invocar CALL set_current_user(user, ip, agent) por solicitud (ya implementado).
- Índices y performance:
  - Índices amplios cubren la mayoría de queries (status, fechas, activos). Validar planes de ejecución en vistas más pesadas (v_inventory_monthly_summary).
- JSON:
  - Campos JSON (permissions, specifications, options, validation_rules) permiten flexibilidad; considerar validaciones a nivel de aplicación.
- Integridad:
  - Estrategias ON DELETE coherentes: CASCADE para dependientes críticos, SET NULL para relaciones débiles. Revisar si coinciden con expectativas del negocio.

Fin del documento.