USE tdm3;
CREATE INDEX idx_audit_module ON audit_logs(module_name, created_at);
CREATE INDEX idx_users_email_status ON users(email, status);
