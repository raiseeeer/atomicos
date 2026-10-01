USE atomicos;

INSERT INTO departments (name) VALUES ('Human Resources'),('Engineering'),('Finance'),('Operations');
INSERT INTO positions (title, base_salary) VALUES ('HR Manager', 45000),('Software Developer', 40000),('Accountant', 32000),('Operations Staff', 22000);

-- Default admin. Username: admin   Password: Admin@123   (CHANGE AFTER FIRST LOGIN)
INSERT INTO users (username, password_hash, role)
VALUES ('admin', '$2b$10$XF.vqoK8I62DtZU.4rsFK.edyZQcZlscv/E/3V3IA3SpK3XU2tz7y', 'admin');
