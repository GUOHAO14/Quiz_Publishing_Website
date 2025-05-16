-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 10:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `morningkdb`
--
CREATE DATABASE IF NOT EXISTS `morningkdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `morningkdb`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`) VALUES
(1, 13),
(3, 27);

-- --------------------------------------------------------

--
-- Table structure for table `answer_selection`
--

CREATE TABLE `answer_selection` (
  `select_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `accuracy` tinyint(1) NOT NULL,
  `quest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answer_selection`
--

INSERT INTO `answer_selection` (`select_id`, `text`, `accuracy`, `quest_id`) VALUES
(1, 'WY', 1, 6),
(2, 'PW', 0, 6),
(3, 'GH', 0, 6),
(4, 'Lily', 0, 6),
(5, 'asnjnciqownfiqnfipqwnfiniqnfiqiwnfwfqwnfiwqniwqnc', 0, 7),
(6, 'iwfqiwqipfeqioveoinvecnoepvnipqenvopnfqpinifnqin', 1, 7),
(7, 'iqenvikiqepvnqeovnqeovmeovmeqovovnqevoevnoqvne', 0, 7),
(9, '14', 0, 8),
(10, '8', 0, 8),
(11, 'A', 1, 13),
(12, '', 0, 13),
(13, 'abc', 1, 14),
(14, '123', 1, 15),
(15, 'ddd', 1, 16),
(16, 'abc', 1, 19),
(17, 'abc', 1, 20),
(18, 'abc', 1, 21),
(19, 'abc', 1, 22),
(20, 'abc', 1, 23),
(21, 'abc', 1, 24),
(22, 'abc', 1, 25),
(23, '', 0, 20),
(24, '', 0, 22),
(25, '', 0, 24),
(26, '', 0, 6),
(27, '', 0, 7),
(28, '10', 1, 8),
(29, '8', 0, 9),
(30, 'ABCDE', 1, 10),
(33, '6', 0, 8),
(34, '4', 0, 8),
(35, '4', 1, 9),
(36, 'Hati', 1, 26),
(37, 'Menghasilkan enzim pencernaan', 0, 27),
(38, 'Menyerap oksigen dan membuang karbon dioksida', 1, 27),
(39, 'Ginjal', 0, 28),
(40, 'Jantung', 1, 28),
(41, 'Usus', 0, 28),
(42, 'swum', 1, 30),
(43, 'broadcast', 1, 29),
(44, 'broadcasted', 0, 29),
(45, 'Kereta', 0, 34),
(46, 'Basikal', 1, 34),
(47, 'Motosikal', 0, 34),
(48, 'Bengkel', 0, 35),
(49, 'Hospital', 1, 35),
(50, 'Pasar', 0, 35),
(51, 'bentuk', 1, 32),
(52, 'helai', 0, 32),
(53, 'gugus', 0, 32),
(54, 'ikat', 1, 31),
(55, 'bidang', 0, 31),
(56, 'cubit', 0, 31),
(57, 'disapu', 0, 33),
(58, 'dicat', 1, 33),
(59, 'dijemur', 0, 33),
(60, '把', 1, 36),
(61, '颗', 1, 37),
(62, '双', 1, 39),
(63, '道', 1, 40),
(64, '张', 1, 38),
(65, '急急忙忙', 1, 41),
(66, '平平安安', 0, 41),
(67, '轻轻松松', 0, 42),
(68, '漂漂亮亮', 1, 42),
(69, '无限', 1, 44),
(70, '一点点', 0, 44),
(71, '太阳', 1, 45),
(72, '喔喔啼', 1, 46),
(73, '狗叫', 0, 46),
(74, '开始', 1, 47),
(75, '写', 1, 48),
(76, '门', 0, 48),
(77, '课', 1, 49),
(78, '学', 1, 50),
(81, '轻轻松松', 1, 43),
(82, 'Geothermal resources', 1, 51),
(83, 'Create a second group of participants with ear infections who use 15 drops a day', 1, 52),
(84, 'Create a second group of participants with ear infections who do not use any ear drops', 0, 52),
(85, 'Growing a whole plant from a single cell', 0, 53),
(86, 'Inserting a gene into plants that makes them resistant to insects', 1, 53),
(87, 'The speed that the Earth rotates around the sun', 1, 54),
(88, 'Observation', 1, 55),
(89, 'The period during which someone has an infection, but is not showing symptoms', 1, 56),
(90, 'The period during which someone builds up immunity to a disease', 0, 56),
(91, 'Greater oxygen production', 1, 57),
(92, 'Acids', 0, 58),
(93, 'Neutral', 1, 58),
(94, 'Gabungan dua atau lebih kata untuk makna baru', 1, 59),
(95, 'Apabila menggunakan awalan', 0, 60),
(96, 'Apabila melibatkan keseluruhan unsur', 1, 60),
(97, 'ABCDE', 1, 11),
(98, 'ABCDE', 1, 12),
(99, 'A', 1, 18),
(100, 'A', 1, 61),
(101, 'A', 1, 62),
(102, 'B', 0, 62),
(103, 'A', 1, 63),
(104, 'A', 1, 64),
(105, 'A', 1, 65),
(106, 'B', 0, 65),
(107, 'A', 1, 66),
(108, 'A', 1, 67),
(109, 'A', 1, 68),
(110, 'B', 0, 68),
(111, 'A', 1, 69),
(112, 'A', 1, 70),
(113, 'B', 0, 70),
(114, 'A', 1, 71),
(115, 'B', 0, 71),
(116, 'A', 1, 72),
(117, 'A', 1, 73),
(118, 'B', 0, 73),
(119, 'A', 1, 74),
(120, 'A', 1, 75);

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE `assessment` (
  `assess_id` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`assess_id`, `grade`, `year`, `subject_id`, `quiz_id`) VALUES
(1, 1, '2024', 4, 1),
(4, 1, '2024', 16, 13),
(5, 1, '2024', 5, 11),
(6, 1, '2024', 16, 9),
(7, 1, '2024', 16, 14),
(8, 1, '2024', 16, 15),
(9, 1, '2024', 11, 16);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_session`
--

CREATE TABLE `assessment_session` (
  `assess_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `time_taken` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(30) NOT NULL,
  `grade` int(11) NOT NULL,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `class_name`, `grade`, `year`) VALUES
(21, 'A', 1, '2024'),
(23, 'B', 2, '2024'),
(25, 'A', 4, '2025'),
(30, 'D', 6, '2024'),
(33, 'I', 1, '2025'),
(34, 'B', 1, '2024'),
(36, 'F', 1, '2025'),
(37, 'C', 1, '2024');

-- --------------------------------------------------------

--
-- Table structure for table `classname`
--

CREATE TABLE `classname` (
  `id` int(11) NOT NULL,
  `class_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classname`
--

INSERT INTO `classname` (`id`, `class_name`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'E'),
(6, 'F'),
(7, 'G'),
(8, 'H'),
(9, 'I'),
(10, 'J');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `subject_id`, `class_id`) VALUES
(13, 15, 21),
(14, 11, 34),
(16, 15, 25),
(17, 4, 23),
(19, 11, 33),
(20, 5, 21),
(21, 18, 34);

-- --------------------------------------------------------

--
-- Table structure for table `course_teacher`
--

CREATE TABLE `course_teacher` (
  `teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_teacher`
--

INSERT INTO `course_teacher` (`teacher_id`, `course_id`) VALUES
(3, 21),
(4, 17),
(5, 19);

-- --------------------------------------------------------

--
-- Table structure for table `enrolment`
--

CREATE TABLE `enrolment` (
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolment`
--

INSERT INTO `enrolment` (`student_id`, `class_id`) VALUES
(5, 34),
(5, 36),
(6, 34),
(7, 34),
(8, 21),
(9, 21),
(12, 23);

-- --------------------------------------------------------

--
-- Table structure for table `exercise`
--

CREATE TABLE `exercise` (
  `exe_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercise`
--

INSERT INTO `exercise` (`exe_id`, `folder_id`, `quiz_id`) VALUES
(10, 16, 7),
(11, 23, 17),
(12, 24, 18),
(13, 16, 29),
(18, 23, 34),
(19, 23, 33),
(20, 23, 32),
(21, 23, 31),
(22, 23, 30),
(23, 33, 24),
(24, 33, 25),
(25, 33, 26),
(26, 33, 27),
(27, 33, 28);

-- --------------------------------------------------------

--
-- Table structure for table `exercise_session`
--

CREATE TABLE `exercise_session` (
  `session_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `exe_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercise_session`
--

INSERT INTO `exercise_session` (`session_id`, `score`, `exe_id`, `student_id`) VALUES
(2, 100.00, 10, 8),
(3, 90.00, 10, 8),
(4, 0.00, 10, 8);

-- --------------------------------------------------------

--
-- Table structure for table `folder`
--

CREATE TABLE `folder` (
  `folder_id` int(11) NOT NULL,
  `folder_name` varchar(30) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folder`
--

INSERT INTO `folder` (`folder_id`, `folder_name`, `course_id`) VALUES
(3, 'Chapter 1', 21),
(12, 'Ayat Aktif & Ayat Pasif', 17),
(13, 'Kegunaan Peribahasa', 17),
(14, 'Sistem Pernafasan Manusia', 20),
(15, 'Comprehension articles', 16),
(16, 'Grammar', 13),
(19, 'Kosa Kata', 17),
(22, 'Vocabulary', 16),
(23, '第一课', 14),
(24, '第二课', 14),
(25, '第三课', 14),
(29, 'Karangan', 17),
(30, 'Tatabahasa', 17),
(31, 'UDIAMDIAM', 17),
(32, 'Novel', 17),
(33, 'CHAPTER', 21);

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE `grade` (
  `id` int(11) NOT NULL,
  `grade_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`id`, `grade_level`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `quest_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`quest_id`, `text`, `quiz_id`, `question_type`) VALUES
(6, 'abc', 7, 'MCQ'),
(7, 'abc', 13, 'MCQ'),
(8, 'Find the limit of a polynomial function: lim(x → -2) (x^2 - 3x + 4)', 13, 'MCQ'),
(9, 'Identify the limit at infinity: lim(x → ∞) (4x / (x + 1))', 13, 'MCQ'),
(10, '', 9, 'shortans'),
(11, 'aknaklfcnnfopqnfopqenvkqenve', 8, 'shortans'),
(12, 'abc', 10, 'shortans'),
(13, '', 11, 'MCQ'),
(14, 'ojownfqniqnefqfqfnoqnfoqwnf', 11, 'shortans'),
(15, 'abc', 11, 'shortans'),
(16, '', 11, 'shortans'),
(18, '123', 13, 'shortans'),
(19, 'babe', 17, 'shortans'),
(20, 'baby', 17, 'MCQ'),
(21, '', 17, 'shortans'),
(22, '', 17, 'MCQ'),
(23, '', 17, 'shortans'),
(24, '', 17, 'MCQ'),
(25, '', 17, 'shortans'),
(26, 'Organ manusia manakah yang bertanggungjawab untuk menapis toksin daripada darah?', 12, 'shortans'),
(27, 'Apakah fungsi utama paru-paru dalam sistem pernafasan manusia?', 12, 'MCQ'),
(28, 'Apakah nama organ yang bertanggungjawab mengepam darah ke seluruh badan?', 12, 'MCQ'),
(29, 'Past tense of broadcast is?', 7, 'MCQ'),
(30, 'Write the past participle of \"swim\".', 7, 'shortans'),
(31, 'Ayah membeli se....... pisang di pasar.', 1, 'MCQ'),
(32, 'Ayah menghadiahkan se........ cincin kepada ibu.', 1, 'MCQ'),
(33, 'Rumah itu ....... oleh Encik Rahman dengan cat yang baru.', 1, 'MCQ'),
(34, 'Azim oergi ke sekolah dengan mengayuh .......', 1, 'MCQ'),
(35, 'Doktor Khusairy bekerja di sebuah ........', 1, 'MCQ'),
(36, '一 ( ) 雨伞', 30, 'shortans'),
(37, '一 ( ) 星星', 30, 'shortans'),
(38, '一 ( ) 笑脸', 30, 'shortans'),
(39, '一 ( ) 翅膀', 30, 'shortans'),
(40, '一 ( ) 彩虹', 30, 'shortans'),
(41, '甘地的鞋子会掉到站台上,因为他________________地要赶上火车。', 31, 'MCQ'),
(42, '姐姐打扮得________________,出席今晚的晚宴。', 31, 'MCQ'),
(43, '松鼠用起重机_______________地搬运木头。', 32, 'shortans'),
(44, '小丑在台上,带给观众_______________的欢笑。', 32, 'MCQ'),
(45, '我打开窗口,看见 _______________ 公公已经露出圆圆胖胖的脸', 33, 'shortans'),
(46, '公鸡张着嘴巴 ________________ ,', 33, 'MCQ'),
(47, ',顽皮的露珠也 _________________ 向我\r\n说声再见。', 33, 'shortans'),
(48, '听( )', 34, 'MCQ'),
(49, '开( )', 34, 'shortans'),
(50, '( )校', 34, 'shortans'),
(51, 'Oil, natural gas and coal are examples of …', 35, 'shortans'),
(52, 'A scientist is conducting a study to determine how well a new medication treats ear infections. The scientist tells the participants to put 10 drops in their infected ear each day. After two weeks, all participants\' ear infections had healed. Which of the following changes to the design of this study would most improve the ability to test if the new medication effectively treats ear infections?', 35, 'MCQ'),
(53, 'Which of the following is an example of genetic engineering?', 36, 'MCQ'),
(54, 'What is the main cause of seasons on the Earth?', 36, 'shortans'),
(55, 'The time a computer takes to start has increased dramatically. One possible explanation for this is that the computer is running out of memory. This explanation is a scientific…', 37, 'shortans'),
(56, 'Many diseases have an incubation period. Which of the following best describes what an incubation period is?', 37, 'MCQ'),
(57, 'When large areas of forest are removed so land can be converted for other uses, such as farming, which of the following occurs?', 38, 'shortans'),
(58, 'An antacid relieves an overly acidic stomach because the main components of antacids are …', 38, 'MCQ'),
(59, 'Apakah yang dimaksudkan dengan kata majmuk?', 39, 'shortans'),
(60, 'Dalam penggandaan yang manakah ejaan menjadi bertambah mantap?', 39, 'MCQ'),
(61, 'abc', 14, 'shortans'),
(62, 'abc', 15, 'MCQ'),
(63, 'abc', 16, 'shortans'),
(64, 'abc', 18, 'shortans'),
(65, 'abc', 19, 'MCQ'),
(66, 'abc', 20, 'shortans'),
(67, 'abc', 21, 'shortans'),
(68, 'abc', 22, 'MCQ'),
(69, 'abc', 23, 'shortans'),
(70, 'abc', 24, 'MCQ'),
(71, 'abc', 25, 'MCQ'),
(72, 'abc', 26, 'shortans'),
(73, 'abc', 27, 'MCQ'),
(74, 'abc', 28, 'shortans'),
(75, 'abc', 29, 'shortans');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL,
  `quiz_name` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `quiz_type` varchar(20) NOT NULL,
  `creation_date` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `quiz_name`, `description`, `quiz_type`, `creation_date`, `status`) VALUES
(1, 'BAHASA MELAYU 1', 'SEMUA MURID MURID SILA JAWAB BAIK BAIK', 'assessment', '2024-12-15 09:01:36', 0),
(7, 'test what\'s going on!', 'sorry, i am straight. What\'s in that cup? Let me see that! Walking to the parking lot, he and his chick pulled down the car window, spit out the chicken that they swallowed the night before. Raw as shit there\'s no way that ass is well done. wesgrfvesejdjekef wefswef wfeews efw sefws efwa efawsefw wefawefawefawefw3RFW AEGRAEWF W3FAERG E ge argawqegawgeew gw.', 'exercise', '2024-12-12 10:56:09', 0),
(8, 'test earn it', 'Cause I\'ma care for u, you you. Yeayy yeay, the way you\'re working. The way you worked it, you earned it.', 'test', '2024-12-12 10:56:09', 0),
(9, 'Mathematics Assessment', 'Discover the foundation of calculus by exploring limits. This quiz helps you understand how functions behave as they approach specific points or infinity.', 'assessment', '2024-11-20 00:00:00', 0),
(10, 'Cina Test', 'Test your knowledge of everyday Mandarin words with this beginner-friendly quiz. Perfect for expanding your basic vocabulary', 'test', '2024-11-01 00:00:00', 0),
(11, 'Assess Sains', 'Organ Manusia: Pelajari organ manusia melalui kuiz interaktif ini.', 'assessment', '2024-11-22 00:00:00', 0),
(12, 'Ujian Sains', 'Uji pemahaman anda tentang konsep asas eksperimen dan teori sains. Murid digalakkan membuat kuiz ini sebagai ulang kaji anda.', 'test', '2024-11-20 00:00:00', 0),
(13, 'Assess MM 2', 'Brush up on addition, subtraction, multiplication, and division skills with this foundational quiz.', 'assessment', '2024-11-01 00:00:00', 0),
(14, 'Assess MM 3', 'Tackle a variety of problems involving fractions, decimals, and their conversions. Learn to simplify fractions, compare decimals, and apply these concepts in everyday scenarios.', 'assessment', '2024-11-02 00:00:00', 0),
(15, 'Assess MM 4', 'A beginner-friendly introduction to algebra, focusing on solving simple equations, combining like terms, and understanding variables. Ideal for those new to algebraic concepts.', 'assessment', '2024-11-22 00:00:00', 0),
(16, 'Assess Cina 2', 'Decode and match the correct meanings of common Chinese characters. Build your recognition skills! Besides, test your knowledge of everyday Mandarin words with this beginner-friendly quiz. Perfect for expanding your basic vocabulary', 'assessment', '2024-11-22 00:00:00', 0),
(17, '华文谚语和古韵', 'Explore famous Chinese idioms and their meanings through fun scenarios and examples. Other than that, you are required to decode and match the correct meanings of old Chinese poems. Build your recognition skills!', 'exercise', '2024-11-01 00:00:00', 0),
(18, '学习生字拼音和词义', 'Practice correct pinyin spelling and vocabulary definition in this quiz.', 'exercise', '2024-11-02 00:00:00', 0),
(19, 'ENG test 1', 'Explore the world of shapes and angles with this interactive quiz. From calculating perimeters and areas to identifying geometric figures, this quiz will make geometry exciting and easy to grasp.', 'test', '2024-11-01 00:00:00', 0),
(20, 'ENG Test 2', 'Dive into practical problems that involve time conversions, unit measurements, and real-life applications. Perfect for learners wanting to connect math concepts to daily life.', 'test', '2024-11-01 00:00:00', 0),
(21, 'ENG test3', 'Challenge yourself with more complex algebraic equations, quadratic expressions, and problem-solving techniques. A great way to prepare for higher-level math studies.', 'test', '2024-11-24 00:00:00', 0),
(22, 'ENG test4', 'Enhance your critical thinking skills with word problems that require careful reading and logical reasoning. This quiz emphasizes real-world applications of mathematical concepts.', 'test', '2024-11-01 00:00:00', 0),
(23, 'ENG test 5', 'Learn the basics of trigonometry, including sine, cosine, and tangent functions. Apply these concepts to solve problems involving right triangles and angles.', 'test', '2024-11-24 00:00:00', 0),
(24, 'ARAB exe1', 'Master the basics of statistics by learning how to calculate and interpret mean, median, mode, and range. A helpful introduction to data analysis.', 'exercise', '2024-12-05 00:00:00', 0),
(25, 'ARAB exe2', 'Explore probability concepts with engaging puzzles and scenarios. Develop a strong understanding of likelihood and make predictions based on given data.', 'exercise', '2024-11-13 00:00:00', 0),
(26, 'ARAB exe3', 'Improve your mental calculation skills with rapid-fire problems that cover addition, multiplication, and more. Test your speed and accuracy as you race against the clock!', 'exercise', '2024-11-26 00:00:00', 0),
(27, 'ARAB exe4', 'Delve into sequences, patterns, and number properties in this quiz. Identify trends and solve problems that test your logical thinking and numerical fluency.', 'exercise', '2024-12-05 00:00:00', 0),
(28, 'ARAB exe5', 'Delve into sequences, patterns, and number properties in this quiz. Identify trends and solve problems that test your logical thinking and numerical fluency. Besides, you will also be exploring probability concepts with engaging puzzles and scenarios.', 'exercise', '2024-12-05 00:00:00', 0),
(29, 'English Tenses', 'Please do this quiz before next week\'s class because I will be discussing each question with you all and review your results. Treat as a revision. Have fun doing, and have a fun weekend!', 'exercise', '2024-12-14 15:02:46', 0),
(30, '练习1', '请同学们好好作答', 'exercise', '2024-12-01 16:32:04', 0),
(31, '练习2', '这是锻炼你们的作文理解', 'exercise', '2024-12-03 16:32:04', 0),
(32, '练习3', '加油', 'exercise', '2024-12-15 09:31:28', 0),
(33, '练习4', '再加油加油', 'exercise', '2024-12-14 16:32:04', 0),
(34, '练习5', '你们可以做到的', 'exercise', '2024-12-15 09:31:28', 0),
(35, 'SAINS TEST A', 'Improve your science now', 'test', '2024-12-01 17:05:51', 0),
(36, 'SAINS TEST B', 'Please do carefully and check before submit', 'test', '2024-12-01 17:05:51', 0),
(37, 'sains sains sains', 'cincai la aiya', 'test', '2024-12-15 10:05:42', 0),
(38, 'come do now', 'COME DO NOW!!!!!!!!!', 'test', '2024-12-15 10:05:42', 0),
(39, 'TATABAHASA UJIAN', '', 'test', '2024-12-15 10:05:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `user_id`) VALUES
(5, 15),
(6, 16),
(7, 19),
(8, 22),
(9, 23),
(12, 26);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_name`) VALUES
(18, 'BAHASA ARAB'),
(11, 'BAHASA CINA'),
(15, 'BAHASA INGGERIS'),
(4, 'BAHASA MELAYU'),
(16, 'MATEMATIK'),
(17, 'PENDIDIKAN MORAL'),
(5, 'SAINS');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `user_id`) VALUES
(3, 17),
(4, 20),
(5, 21);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `test_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`test_id`, `course_id`, `quiz_id`) VALUES
(3, 21, 8),
(4, 14, 10),
(6, 20, 12),
(10, 20, 35),
(11, 20, 36),
(12, 20, 37),
(13, 20, 38),
(14, 17, 39),
(15, 13, 19),
(16, 13, 20),
(17, 13, 21),
(18, 13, 22),
(19, 13, 23);

-- --------------------------------------------------------

--
-- Table structure for table `test_session`
--

CREATE TABLE `test_session` (
  `test_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `time_taken` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_session`
--

INSERT INTO `test_session` (`test_id`, `student_id`, `score`, `time_taken`) VALUES
(3, 5, 75.00, '00:09:46'),
(3, 6, 80.00, '00:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(70) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `photo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `role`, `dob`, `photo`) VALUES
(13, 'King', '$2y$10$KlXZnJZYhhexTQahVI9lfeJFcuAKf0x9cF7j7E3UtmWhJ6gWwOndG', 'masterkey@gmail.com', 'admin', '2024-11-02', NULL),
(15, 'Huey Ling', '$2y$10$xf2AhxjrufiD3nqvccYgyuaqmKjYu6hfQjptonCY8uEx0YT.Di7Ba', 'hueyling@yahoo.com', 'student', '2024-11-01', NULL),
(16, 'fsdsadf', '$2y$10$hozPSxhPXY0W/Wl8Iu3nLOtn3oWEC0umTQsnJekvYGFrfB0R8bKrG', 'asfd@', 'student', '2024-11-28', NULL),
(17, 'Anwar', '$2y$10$CKZW/BJlHCf7eNN.Z/Ed.eebttFMxcxMgLlKqLq4NaEVwcwg1T7iy', 'anwar@gmail.com', 'teacher', '2024-11-27', NULL),
(19, 'hogrider', '$2y$10$Id0WM0PE60f7ahymk7nN9efeQ9fTHzOhERJIGEOLL9RthmZQAVCAO', 'hog@gmail.com', 'student', '1990-09-11', 'uploads/profile19.png'),
(20, 'Barbarian', '$2y$10$2RIjssVn9sJdTWi.kEu4TugWL7OfQ5/ZT4UWGJ6ZsIr8HTdoFTU6m', 'barb@gmail.com', 'teacher', '2024-11-28', NULL),
(21, 'Joshua', '$2y$10$NZK02rtgrhvG4GBlJwya9OlIKqXbjWyJQH21.LyBcr.AXp/m.lP9m', 'joshua@gmail.com', 'teacher', '2024-07-19', NULL),
(22, 'Najib', '$2y$10$EGqn/6wUFLFBqTOFwDOLjepIfif9abxX/.oAMCcnKAKCRTA2qHc16', 'najib@gmail.com', 'student', '2024-10-25', 'uploads/profile22.jpg'),
(23, 'marshall', '$2y$10$Up6EAG1vhpDOIFRhUTVkmeArGowTFoe33oh2Xgm9yWCfbrDOgoPjq', 'marshall@gmail.com', 'student', '2024-02-16', NULL),
(26, 'Wen Yew', '$2y$10$9v.DB.qZQHY0jvs7ifZGkezUz.nmK06syMCc1HA/RLW8T5zg27RHa', 'wenyew@gmail.com', 'student', '2023-11-09', NULL),
(27, 'Queen', '$2y$10$MuXNtpCqpUIPoNQTEoG6Y.YXs6LbDO5P/MHbYnIvw/ywos8XACib.', 'queen@gmail.com', 'admin', '2023-10-11', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_ibfk_1` (`user_id`);

--
-- Indexes for table `answer_selection`
--
ALTER TABLE `answer_selection`
  ADD PRIMARY KEY (`select_id`),
  ADD KEY `answer_selection_ibfk_1` (`quest_id`);

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`assess_id`),
  ADD KEY `grade` (`grade`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `assessment_session`
--
ALTER TABLE `assessment_session`
  ADD PRIMARY KEY (`assess_id`,`student_id`),
  ADD KEY `assessment_session_ibfk_1` (`student_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `class_ibfk_1` (`grade`),
  ADD KEY `class_name_fk` (`class_name`);

--
-- Indexes for table `classname`
--
ALTER TABLE `classname`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_name` (`class_name`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `course_ibfk_1` (`subject_id`),
  ADD KEY `course_ibfk_2` (`class_id`);

--
-- Indexes for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD PRIMARY KEY (`teacher_id`,`course_id`),
  ADD KEY `course_teacher_ibfk_2` (`course_id`);

--
-- Indexes for table `enrolment`
--
ALTER TABLE `enrolment`
  ADD PRIMARY KEY (`student_id`,`class_id`),
  ADD KEY `enrolment_ibfk_1` (`class_id`);

--
-- Indexes for table `exercise`
--
ALTER TABLE `exercise`
  ADD PRIMARY KEY (`exe_id`),
  ADD KEY `exercise_ibfk_1` (`folder_id`),
  ADD KEY `exercise_ibfk_2` (`quiz_id`);

--
-- Indexes for table `exercise_session`
--
ALTER TABLE `exercise_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `exercise_session_ibfk_1` (`exe_id`),
  ADD KEY `exercise_session_ibfk_2` (`student_id`);

--
-- Indexes for table `folder`
--
ALTER TABLE `folder`
  ADD PRIMARY KEY (`folder_id`),
  ADD KEY `folder_ibfk_1` (`course_id`);

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_level` (`grade_level`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`quest_id`),
  ADD KEY `question_ibfk_1` (`quiz_id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `student_ibfk_1` (`user_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `teacher_ibfk_1` (`user_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `test_ibfk_1` (`course_id`),
  ADD KEY `test_ibfk_2` (`quiz_id`);

--
-- Indexes for table `test_session`
--
ALTER TABLE `test_session`
  ADD PRIMARY KEY (`test_id`,`student_id`),
  ADD KEY `test_session_ibfk_2` (`student_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `answer_selection`
--
ALTER TABLE `answer_selection`
  MODIFY `select_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `assessment`
--
ALTER TABLE `assessment`
  MODIFY `assess_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `classname`
--
ALTER TABLE `classname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `exercise`
--
ALTER TABLE `exercise`
  MODIFY `exe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `exercise_session`
--
ALTER TABLE `exercise_session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `folder`
--
ALTER TABLE `folder`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `quest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `answer_selection`
--
ALTER TABLE `answer_selection`
  ADD CONSTRAINT `answer_selection_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `question` (`quest_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `assessment_ibfk_1` FOREIGN KEY (`grade`) REFERENCES `grade` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessment_session`
--
ALTER TABLE `assessment_session`
  ADD CONSTRAINT `assessment_session_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assessment_session_ibfk_2` FOREIGN KEY (`assess_id`) REFERENCES `assessment` (`assess_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`grade`) REFERENCES `grade` (`grade_level`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_name_fk` FOREIGN KEY (`class_name`) REFERENCES `classname` (`class_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_teacher`
--
ALTER TABLE `course_teacher`
  ADD CONSTRAINT `course_teacher_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_teacher_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrolment`
--
ALTER TABLE `enrolment`
  ADD CONSTRAINT `enrolment_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrolment_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exercise`
--
ALTER TABLE `exercise`
  ADD CONSTRAINT `exercise_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exercise_session`
--
ALTER TABLE `exercise_session`
  ADD CONSTRAINT `exercise_session_ibfk_1` FOREIGN KEY (`exe_id`) REFERENCES `exercise` (`exe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_session_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `folder`
--
ALTER TABLE `folder`
  ADD CONSTRAINT `folder_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `test_session`
--
ALTER TABLE `test_session`
  ADD CONSTRAINT `test_session_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_session_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
