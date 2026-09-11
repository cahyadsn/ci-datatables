-- Sample Database Schema & Data for ci-datatables sample application

DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `departments`;

CREATE TABLE `departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `departments` (`id`, `name`, `description`) VALUES
(1, 'Engineering', 'Software engineering and IT operations'),
(2, 'Marketing', 'Brand management and digital marketing'),
(3, 'Human Resources', 'Talent acquisition and people operations'),
(4, 'Finance', 'Accounting, financial planning, and payroll'),
(5, 'Product Design', 'UI/UX design and product research');

CREATE TABLE `employees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_department` (`department_id`),
  CONSTRAINT `fk_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `department_id`, `position`, `salary`, `status`, `created_at`) VALUES
(1, 'James', 'Smith', 'james.smith@example.com', 1, 'Software Engineer', 45000.00, 'Active', '2024-01-15 00:00:00'),
(2, 'Mary', 'Johnson', 'mary.johnson@example.com', 2, 'SEO Specialist', 46250.00, 'Active', '2024-01-20 00:00:00'),
(3, 'Robert', 'Williams', 'robert.williams@example.com', 3, 'People Partner', 47500.00, 'Active', '2024-01-25 00:00:00'),
(4, 'Patricia', 'Brown', 'patricia.brown@example.com', 4, 'Controller', 48750.00, 'Active', '2024-01-30 00:00:00'),
(5, 'John', 'Jones', 'john.jones@example.com', 5, 'UI/UX Designer', 50000.00, 'Inactive', '2024-02-04 00:00:00'),
(6, 'Jennifer', 'Garcia', 'jennifer.garcia@example.com', 1, 'Senior Backend Engineer', 51250.00, 'On Leave', '2024-02-09 00:00:00'),
(7, 'Michael', 'Miller', 'michael.miller@example.com', 2, 'Growth Marketer', 52500.00, 'Active', '2024-02-14 00:00:00'),
(8, 'Linda', 'Davis', 'linda.davis@example.com', 3, 'HR Specialist', 53750.00, 'Active', '2024-02-19 00:00:00'),
(9, 'David', 'Rodriguez', 'david.rodriguez@example.com', 4, 'Senior Accountant', 55000.00, 'Active', '2024-02-24 00:00:00'),
(10, 'Elizabeth', 'Martinez', 'elizabeth.martinez@example.com', 5, 'Lead Product Designer', 56250.00, 'Active', '2024-02-29 00:00:00'),
(11, 'William', 'Hernandez', 'william.hernandez@example.com', 1, 'DevOps Specialist', 57500.00, 'Inactive', '2024-03-05 00:00:00'),
(12, 'Barbara', 'Lopez', 'barbara.lopez@example.com', 2, 'Marketing Lead', 58750.00, 'On Leave', '2024-03-10 00:00:00'),
(13, 'Richard', 'Gonzalez', 'richard.gonzalez@example.com', 3, 'HR Manager', 60000.00, 'Active', '2024-03-15 00:00:00'),
(14, 'Susan', 'Wilson', 'susan.wilson@example.com', 4, 'Financial Analyst', 61250.00, 'Active', '2024-03-20 00:00:00'),
(15, 'Joseph', 'Anderson', 'joseph.anderson@example.com', 5, 'Graphic Artist', 62500.00, 'Active', '2024-03-25 00:00:00'),
(16, 'Jessica', 'Thomas', 'jessica.thomas@example.com', 1, 'QA Engineer', 63750.00, 'Active', '2024-03-30 00:00:00'),
(17, 'Thomas', 'Taylor', 'thomas.taylor@example.com', 2, 'Content Strategist', 65000.00, 'Inactive', '2024-04-04 00:00:00'),
(18, 'Sarah', 'Moore', 'sarah.moore@example.com', 3, 'Talent Recruiter', 66250.00, 'On Leave', '2024-04-09 00:00:00'),
(19, 'Charles', 'Jackson', 'charles.jackson@example.com', 4, 'Payroll Specialist', 67500.00, 'Active', '2024-04-14 00:00:00'),
(20, 'Karen', 'Martin', 'karen.martin@example.com', 5, 'UX Researcher', 68750.00, 'Active', '2024-04-19 00:00:00'),
(21, 'Christopher', 'Lee', 'christopher.lee@example.com', 1, 'Full Stack Developer', 70000.00, 'Active', '2024-04-24 00:00:00'),
(22, 'Nancy', 'Perez', 'nancy.perez@example.com', 2, 'SEO Specialist', 71250.00, 'Active', '2024-04-29 00:00:00'),
(23, 'Daniel', 'Thompson', 'daniel.thompson@example.com', 3, 'People Partner', 72500.00, 'Inactive', '2024-05-04 00:00:00'),
(24, 'Lisa', 'White', 'lisa.white@example.com', 4, 'Controller', 73750.00, 'On Leave', '2024-05-09 00:00:00'),
(25, 'Matthew', 'Harris', 'matthew.harris@example.com', 5, 'UI/UX Designer', 75000.00, 'Active', '2024-05-14 00:00:00');
