-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2025 at 03:01 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `faodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '202cb962ac59075b964b07152d234b70', '2025-05-29 12:33:20');

-- --------------------------------------------------------

--
-- Table structure for table `extension_worker`
--

CREATE TABLE `extension_worker` (
  `worker_id` int(11) NOT NULL,
  `worker_first_name` varchar(30) NOT NULL,
  `worker_last_name` varchar(30) NOT NULL,
  `worker_phone` varchar(30) NOT NULL,
  `worker_email` varchar(30) NOT NULL,
  `worker_password` varchar(30) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `extension_worker`
--

INSERT INTO `extension_worker` (`worker_id`, `worker_first_name`, `worker_last_name`, `worker_phone`, `worker_email`, `worker_password`, `role`) VALUES
(6, 'Leatitia', 'UMUBYEYI', '0786329763', 'aa@gmail.com', '123', 'Doctor'),
(7, 'teta', 'chance', '0786329763', 'tetachance45@gmail.com', '123', 'Doctor');

-- --------------------------------------------------------

--
-- Table structure for table `faostock`
--

CREATE TABLE `faostock` (
  `medecine_id` int(11) NOT NULL,
  `medecine` varchar(30) NOT NULL,
  `quantity` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `faostock`
--

INSERT INTO `faostock` (`medecine_id`, `medecine`, `quantity`) VALUES
(1, 'Urukingo Rwa Newcastle', 2),
(2, 'Umuti wica udukoko', 4);

-- --------------------------------------------------------

--
-- Table structure for table `medecine_usage`
--

CREATE TABLE `medecine_usage` (
  `usage_id` int(11) NOT NULL,
  `medecine_id` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medecine_usage`
--

INSERT INTO `medecine_usage` (`usage_id`, `medecine_id`, `fid`, `status`) VALUES
(1, 1, 38, 'treated'),
(2, 2, 38, 'treated');

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE `support` (
  `support_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(30) NOT NULL,
  `message` varchar(200) NOT NULL,
  `response` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `support`
--

INSERT INTO `support` (`support_id`, `id`, `name`, `email`, `message`, `response`) VALUES
(2, 7, 'mmmmmm', 'aaaa@gmail.com', 'jjjjjjj', 'yes, you have to ..'),
(3, 7, 'patience kabatesi', 'tesipatience15@gmail.com', 'mwiriwe neza, nashakaga gusaba ubufasha bujyanye nibihingwa', 'mwiriwe neza patience, tubafashe kubyerekeye iki');

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `symptom_id` int(11) NOT NULL,
  `symptoms` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`symptom_id`, `symptoms`) VALUES
(1, 'Kurohama'),
(2, 'Guta umutwe'),
(3, 'Kwivunika ku mubiri w’ifi'),
(4, 'Guhumeka nabi'),
(5, 'Gutura inda'),
(6, 'Ububabare mu mabere'),
(7, 'Utubara twera ku mubiri'),
(8, 'Gufungura umunwa byihuse'),
(9, 'Umuriro ukabije'),
(10, 'Igabanuka ry\'ibiro'),
(11, 'Kutarya'),
(12, 'Guhitwa'),
(13, 'Amababi asatagurika'),
(14, 'Gukanyagwa ku mbuto'),
(15, 'Gutakaza ubwenge'),
(16, 'Kunanirwa guhaguruka'),
(17, 'Uduheri ku mababi'),
(18, 'Amababi ahera vuba'),
(52, 'Imyobo mu myanya y’igor'),
(53, 'Imyobo mu myanya y’igori'),
(54, 'amababi agakuka');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(120) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`id`, `categoryName`, `CreationDate`, `UpdationDate`) VALUES
(1, 'Agriculture', '2024-05-01 16:24:34', '2025-02-22 21:03:36'),
(8, 'Livestock', '2025-02-22 20:57:19', NULL),
(9, 'Fishing', '2025-02-22 21:07:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusinfo`
--

CREATE TABLE `tblcontactusinfo` (
  `id` int(11) NOT NULL,
  `Address` tinytext DEFAULT NULL,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblcontactusinfo`
--

INSERT INTO `tblcontactusinfo` (`id`, `Address`, `EmailId`, `ContactNo`) VALUES
(1, 'World wide', 'fao@gmail.com', '25078974561');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusquery`
--

CREATE TABLE `tblcontactusquery` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblcontactusquery`
--

INSERT INTO `tblcontactusquery` (`id`, `name`, `EmailId`, `ContactNumber`, `Message`, `PostingDate`, `status`) VALUES
(1, 'Kunal ', 'kunal@gmail.com', '7977779798', 'I want to know you brach in Chandigarh?', '2024-06-04 09:34:51', 1),
(2, 'patience kabatesi', 'tesipatience15@gmail.com', '0790204864', 'mwaramutse neza FAO banyita mukamana nashakaga kubasaba ko mwazasura ikiraro cyanjye', '2025-07-30 15:41:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbldeseases`
--

CREATE TABLE `tbldeseases` (
  `id` int(11) NOT NULL,
  `deseaseTitle` varchar(150) DEFAULT NULL,
  `deseaseCategory` int(11) DEFAULT NULL,
  `deseaseOverview` longtext DEFAULT NULL,
  `Vimage1` varchar(120) DEFAULT NULL,
  `Vimage2` varchar(120) DEFAULT NULL,
  `Vimage3` varchar(120) DEFAULT NULL,
  `Vimage4` varchar(120) DEFAULT NULL,
  `Vimage5` varchar(120) DEFAULT NULL,
  `desease_symptoms` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `desease_prevention` varchar(50000) NOT NULL,
  `treatment` varchar(50) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbldeseases`
--

INSERT INTO `tbldeseases` (`id`, `deseaseTitle`, `deseaseCategory`, `deseaseOverview`, `Vimage1`, `Vimage2`, `Vimage3`, `Vimage4`, `Vimage5`, `desease_symptoms`, `desease_prevention`, `treatment`, `RegDate`, `UpdationDate`) VALUES
(13, 'Newcastle (Inkoko)', 8, 'Newcastle ni indwara yibasira cyane inkoko, iterwa na virusi. Irangwa no kurohama, gutakaza imbaraga, no kugabanuka kw’umusaruro w’amagi.', 'Newcastle .jpg', 'newcastle2.png', 'newcastle3.png', 'newcastle4.png', '', 'Kurohama, Guta umutwe', 'Gukingira inkoko hakoreshejwe urukingo rwa Newcastle,Kwirinda gutumiza cyangwa kwimura inkoko zitapimwe', 'Urukingo Rwa Newcastle', '2025-07-19 09:57:19', '2025-08-12 06:10:57'),
(14, 'Aeromonas (mu mafi)', 9, 'Aeromonas ni mikorobi yibasira amafi, itera kubyimba, kuvunika ku mubiri w’ifi, no kwitwara nabi. Yandurira mu mazi yanduye.', 'aeromonas.jpg', 'aeromonas2.jpg', 'aeromonas3.jpg', 'aeronas4.jpg', '', 'Kwivunika ku mubiri w’ifi, Guhumeka nabi', 'Gukoresha amazi meza kandi yuje isuku mu byuzi,Kugaburira amafi indyo yuzuye kandi ifite isuku,Kwirinda guhuriza amafi menshi mu gace gato', 'Urukingo Rwa Newcastle', '2025-07-19 10:10:20', '2025-08-12 06:11:14'),
(15, ' Brucellosis (mu matungo magufi n’amaremare)', 8, 'Brucellosis ni indwara iterwa na bagiteri, yibasira inka, ihene, intama n’ingurube. Itera gutura inda, ibibazo byo mu mabere n’uburumbuke buke.', 'brucellosis.jpg', 'brucellosis2.jpg', 'brucellosis3.jpg', 'brucellosis4.jpg', '', 'Gutura inda, Ububabare mu mabere', 'Gukingira amatungo no kuyapima buri gihe,Gukura amatungo arwaye mu yandi,Kwita ku isuku ry’aho amatungo abarizwa', '', '2025-07-19 10:13:16', NULL),
(16, 'Columnaris (mu mafi)', 9, 'Columnaris ni indwara y’amafi iterwa na bagiteri. Irangwa no gutakaza uruhu, utubara twera ku mubiri, no guhumeka nabi.', 'columnaris.jpg', 'columnaris2.jpg', 'columnaris3.jpg', 'columnaris4.jpg', '', 'Utubara twera ku mubiri, Gufungura umunwa byihuse', 'Kugenzura ubushyuhe n’isuku y’amazi,Kwirinda guhangabanya amafi (stress)', '', '2025-07-19 10:54:21', NULL),
(17, 'East Coast Fever (mu nka)', 8, 'East Coast Fever ni indwara ikomeye y’inka iterwa na paraziti itwarwa n’isazi. Irangwa n’umuriro mwinshi, kubura imbaraga, no gupfa mu gihe gito.', 'eastfever.png', 'eastfever2.jpg', 'eastfever3.jpg', 'eastfever4.jpg', '', 'Umuriro ukabije, Igabanuka ry', 'Gukingira inka urukingo rwa East Coast Fever,Kurwanya ibisazi bitera indwara (gusiga imiti ku ruhu)', '', '2025-07-19 10:58:37', NULL),
(18, ' Ifumbi (ku bihingwa)', 1, 'Ifumbi ni indwara iterwa na bagiteri cyangwa udukoko duto dushobora kwangiza ibimera. Igaragara mu mababi no mu mizi, igatera kumungwa no kubora.', 'ifumbi.jpg', 'ifumbi2.jpg', 'ifumbi3.jpg', 'ifumbi4.jpg', '', 'Kutarya, Guhitwa', 'Guhinga imbuto zizewe kandi zidafite indwara,Guhingira igihe, no gusimburanya imyaka', '', '2025-07-19 11:01:52', NULL),
(19, ' Imyate (ku bihingwa)', 1, 'Imyate ni udukoko twangiza ibihingwa, turya amababi, imbuto n’imizi. Bigabanya umusaruro kandi bigatera gupfa kw’ibihingwa.', 'imyate.jpg', 'imyate2.jpg', 'imyate3.jpg', 'imyate4.jpg', '', 'Amababi asatagurika, Gukanyagwa ku mbuto', 'Gukoresha imiti yica udukoko (pesticides) ku gihe,Guhinga imyaka yihanganira udukoko', '', '2025-07-19 11:06:44', NULL),
(20, '???? Isereri (mu matungo)', 8, 'Isereri ni indwara ituma amatungo atitwara neza, aribwa umutwe, atakaza imbaraga, rimwe na rimwe agatakaza ubwenge cyangwa akagwa.', 'isereri.jpg', 'isereri2.jpg', 'isereri3.jpg', 'isereri4.jpg', '', 'Gutakaza ubwenge, Kunanirwa guhaguruka', 'Kugaburira amatungo indyo yuzuye kandi ifite isuku,Kwirinda kubakingira aho hari amahindu cyangwa izuba ryinshi', '', '2025-07-19 11:09:28', NULL),
(21, '???? Urushimwa (ku bihingwa)', 1, 'Urushimwa ni udukoko dutera uduheri ku mababi y’ibihingwa, tugatuma ahindura ibara, akuma cyangwa agasatagurika, bigatuma igihingwa kidatanga umusaruro.', 'urushimwa.jpg', 'urushimwa2.jpg', 'urushimwa3.jpg', 'urushimwa4.jpg', '', 'Uduheri ku mababi, Amababi ahera vuba', 'Gutera imiti yica udukoko igihe bimenyekanye,Guhinga imbuto zifite ubudahangarwa', '', '2025-07-19 11:13:09', NULL),
(22, 'Ugushe mu Bigori', 1, 'Bikozwa n’udukoko dutera mu bigori, bigatuma ubushobozi bwo gutanga ibiribwa bw’igori bugabanuka', 'ugushe1.jpg', 'ugushe3.jpg', 'ugushe2.jpg', 'ugushe.jpg', '', 'Imyobo mu myanya y’igori, amababi agakuka', 'Gukoresha ubwoko bw’igori bukomeye, gushyira udukingirizo tw’udukoko, no guhinduranya ibihingwa', '', '2025-07-31 14:24:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblfarmers`
--

CREATE TABLE `tblfarmers` (
  `id` int(11) NOT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblfarmers`
--

INSERT INTO `tblfarmers` (`id`, `FullName`, `EmailId`, `Password`, `ContactNo`, `region`, `RegDate`, `UpdationDate`) VALUES
(6, 'ISHIMWE Patrick', 'mugisha1@gmail.com', '$2y$10$av7G7lt1kBNa6WUAhkoSz.KSAxBFA4dyV4NKmhbklyrnqOPAn1quS', '0786329763', 'Muhanga', '2025-02-25 16:55:11', '2025-07-07 23:51:52'),
(7, 'Leatitia UMUBYEYI', 'tesipatience15@gmail.com', '$2y$10$4Si3zhSJIfZ2ZjPaoAOWOuVgj0R9vSUur1Rn9mSdALIqdCnw7hYSe', '0786329763', 'Gisagara', '2025-02-26 23:33:54', '2025-07-10 19:27:19'),
(8, 'teta', 'tetachance45@gmail.com', '$2y$10$B.8A7ji/35EYT9RH0QsnB.Z7cPDq/g6y6mxikqyMqKc4tLtEyYXMC', '0788765432', 'Gasabo', '2025-07-19 10:00:12', NULL),
(9, 'teta', 'patty25@gmail.com', '$2y$10$K/K0Mlc7sGdSf5fgeY2XLOdD9SCYn5uGbFLZbthDc0SBIm6sbsgya', '0790204864', 'Gasabo', '2025-07-19 19:41:55', NULL),
(10, 'jay', 'ajay@gmail.com', '$2y$10$/mbTmWKfln0ZUQxl1YFFT.jFdnRqD.OLJIcsgtrN3RT0vGs5cI2jO', '0785950808', 'Nyarugenge', '2025-07-19 19:42:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblfound_symptoms`
--

CREATE TABLE `tblfound_symptoms` (
  `fid` int(11) NOT NULL,
  `requestno` bigint(12) DEFAULT NULL,
  `dis_id` int(11) NOT NULL,
  `found_symptoms` mediumtext CHARACTER SET utf8 NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblfound_symptoms`
--

INSERT INTO `tblfound_symptoms` (`fid`, `requestno`, `dis_id`, `found_symptoms`, `userEmail`, `ToDate`, `Status`, `PostingDate`, `LastUpdationDate`) VALUES
(12, 363360415, 1, 'Image-based Match', 'patrick@gmail.com', '2025-07-05', 0, '2025-07-05 15:56:56', '2025-07-17 22:40:44'),
(13, 709476280, 8, 'Image-based Match (97% confidence)', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:07:04', '2025-07-17 22:40:48'),
(14, 865276943, 0, 'Image-based Match (72% confidence)', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:08:20', '2025-07-06 06:26:42'),
(15, 230245425, 0, 'Image-based Match (66% confidence)', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:10:23', '2025-07-06 06:26:27'),
(16, 518606943, 0, 'Image-based Match (63% confidence)', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:21:04', '2025-07-06 06:26:15'),
(17, 595100345, 0, 'Image-based Match (67% confidence)', 'patrick@gmail.com', '2025-07-05', 0, '2025-07-05 16:22:52', '2025-07-06 06:25:59'),
(18, 222342752, 0, 'Image-based match with 59% confidence', 'patrick@gmail.com', '2025-07-05', 0, '2025-07-05 16:33:06', '2025-07-06 06:25:48'),
(19, 535402190, 0, 'Image-based match with 63% confidence', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:33:53', '2025-07-06 06:25:35'),
(20, 902283075, 0, 'Image-based match with 66% confidence', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:44:13', '2025-07-06 06:25:18'),
(21, 240659047, 8, 'Image-based match with 97% confidence', 'mugisha@gmail.com', '2025-07-05', 0, '2025-07-05 16:58:28', '2025-07-17 22:40:53'),
(22, 205667136, 0, 'Image-based match with 97% confidence', 'mugisha1@gmail.com', '2025-07-05', 0, '2025-07-06 06:59:42', '2025-07-06 07:06:57'),
(23, 671866240, 0, 'Browning inside stems', 'mugisha1@gmail.com', '2025-07-06', 0, '2025-07-06 07:04:31', '2025-07-06 07:07:18'),
(24, 507035461, 9, 'Guhumeka nabi no gukorora k’umutungo, Kugabanuka kw’ibiro', 'muhire@gmail.com', '2025-07-06', 0, '2025-07-07 23:39:39', '2025-07-17 22:29:21'),
(25, 962336806, 9, 'Image-based match with 97% confidence', 'muhire@gmail.com', '2025-07-05', 0, '2025-07-07 23:40:14', '2025-07-17 22:29:31'),
(26, 911019983, 0, 'Image-based match with 81% confidence', 'mugisha@gmail.com', '2025-07-10', 0, '2025-07-10 19:10:52', NULL),
(28, 983062605, 9, 'Guhumeka nabi no gukorora k’umutungo, Kugabanuka kw’ibiro', 'mugisha1@gmail.com', '2025-07-18', 1, '2025-07-17 22:39:47', '2025-07-18 00:44:20'),
(30, 679844671, 19, 'Image-based match with 100% confidence', 'patty25@gmail.com', '2025-07-19', 1, '2025-07-19 20:44:26', '2025-07-21 10:04:31'),
(31, 313467631, 14, 'Image-based match with 100% confidence', 'patty25@gmail.com', '2025-07-19', 0, '2025-07-19 20:48:42', '2025-07-19 20:48:42'),
(32, 947865409, 15, 'Image-based match with 100% confidence', 'ajay@gmail.com', '2025-07-19', 0, '2025-07-19 20:50:05', '2025-07-19 20:50:05'),
(38, 355340380, 18, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-07-21', 0, '2025-07-21 10:36:18', '2025-07-21 10:36:18'),
(39, 798746536, 13, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-07-21', 1, '2025-07-21 11:44:50', '2025-07-21 11:45:34'),
(40, 621301841, 14, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-07-21', 1, '2025-07-21 12:13:32', '2025-07-21 12:13:59'),
(41, 873805878, 18, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-07-30', 0, '2025-07-30 14:31:50', '2025-07-30 14:31:50'),
(42, 367888370, 16, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-07-30', 1, '2025-07-30 15:53:46', '2025-07-30 15:54:39'),
(43, 393153451, 15, 'Image-based match with 70% confidence', 'tesipatience15@gmail.com', '2025-08-06', 0, '2025-08-06 18:35:48', '2025-08-06 18:35:48'),
(44, 973942764, 14, 'Image-based match with 81% confidence', 'tesipatience15@gmail.com', '2025-08-06', 0, '2025-08-06 18:40:06', '2025-08-06 18:40:06'),
(45, 464055301, 19, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-08-07', 1, '2025-08-07 12:05:21', '2025-08-07 12:09:46'),
(46, 283933867, 17, 'Image-based match with 100% confidence', 'tesipatience15@gmail.com', '2025-08-07', 0, '2025-08-07 12:27:13', '2025-08-07 12:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblpages`
--

INSERT INTO `tblpages` (`id`, `PageName`, `type`, `detail`) VALUES
(1, 'Terms and Conditions', 'terms', '<P align=justify><FONT size=2><STRONG><FONT color=#990000>(1) ACCEPTANCE OF TERMS</FONT><BR><BR></STRONG>Welcome to Yahoo! India. 1Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: <A href=\"http://in.docs.yahoo.com/info/terms/\">http://in.docs.yahoo.com/info/terms/</A>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>'),
(2, 'Privacy Policy', 'privacy', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'Ibyerekeye Twe', 'aboutus', '<span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 13.3333px;\">\r\nDutanga uburyo butandukanye bwo gufasha abahinzi n’aborozi dukoresheje ikoranabuhanga ryo gusesengura amafoto. Iri koranabuhanga rifasha kumenya ibimenyetso by’indwara ku bihingwa, ku matungo n’amafi hakiri kare, bigafasha mu gukumira no kuvura hakiri kare.\r\nNubwo tudakorana n’uruganda runaka rukora ibikoresho, dufite ubushobozi bwo gutanga serivisi zitandukanye zishingiye ku bwoko butandukanye bw’ibimera n’amatungo, bikorohereza buri muhinzi kubona ibisubizo bimubereye.Intego yacu ni ukuba urubuga rwizewe kandi ruhoraho mu gutanga ubujyanama n’ubuyobozi buhamye ku bahinzi n’aborozi, duhereye ku isesengura ry’amafoto n’ibimenyetso bigaragara ku bihingwa cyangwa amatungo, bityo tugafasha kongera umusaruro no kugabanya igihombo gituruka ku ndwara.\r\n</span><span style=\"color: rgb(52, 52, 52); font-family: Arial, Helvetica, sans-serif;\">\r\n'),
(11, 'FAQs', 'faqs', '<!DOCTYPE html>\r\n<html lang=\"rw\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>FAQ - Indwara mu Bihingwa, Amatungo, n’Amafi</title>\r\n    <link href=\"https://fonts.googleapis.com/css2?family=Open+Sans&display=swap\" rel=\"stylesheet\">\r\n    <style>\r\n        .faq-section {\r\n            color: rgb(0, 100, 0); /* Dark green text */\r\n            font-family: \"Open Sans\", Arial, sans-serif;\r\n            font-size: 14px;\r\n            text-align: justify;\r\n            margin: 20px;\r\n            max-width: 800px;\r\n            background-color: #f5fff5; /* Light green background */\r\n            padding: 20px;\r\n            border-radius: 8px;\r\n        }\r\n        .faq-section h1 {\r\n            font-size: 20px;\r\n            font-weight: bold;\r\n            margin-bottom: 15px;\r\n            color: #004d00; /* Darker green for heading */\r\n        }\r\n        .faq-item {\r\n            margin-bottom: 10px;\r\n        }\r\n        .faq-question {\r\n            font-size: 16px;\r\n            font-weight: bold;\r\n            cursor: pointer;\r\n            padding: 10px;\r\n            background-color: #d4edda; /* Light green for question background */\r\n            border: 1px solid #28a745; /* Green border */\r\n            border-radius: 5px;\r\n            color: #155724; /* Dark green text */\r\n        }\r\n        .faq-question:hover {\r\n            background-color: #c3e6cb; /* Slightly darker green on hover */\r\n        }\r\n        .faq-question::after {\r\n            content: \'+\';\r\n            font-size: 18px;\r\n            color: #28a745;\r\n            float: right;\r\n        }\r\n        .faq-question.active::after {\r\n            content: \'?\';\r\n        }\r\n        .faq-answer {\r\n            display: none;\r\n            padding: 10px;\r\n            border-left: 3px solid #28a745; /* Green border for answer */\r\n            margin-top: 5px;\r\n            background-color: #ffffff; /* White background for answer */\r\n        }\r\n        .faq-answer.active {\r\n            display: block;\r\n        }\r\n        .farmer-name {\r\n            font-style: italic;\r\n            color: #6c757d; /* Gray for attribution to contrast with green */\r\n            font-size: 12px;\r\n            margin-top: 5px;\r\n        }\r\n        .video-container {\r\n            margin: 20px 0;\r\n        }\r\n        .video-container video {\r\n            width: 100%;\r\n            max-width: 800px;\r\n            border: 2px solid #28a745; /* Green border for video */\r\n            border-radius: 5px;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"faq-section\">\r\n        <h1>Ibibazo Bikunzwe Kubazwa (FAQ) Ku Burwayi mu Bihingwa, Amatungo, n’Amafi</h1>\r\n\r\n        <div class=\"faq-item\">\r\n            <div class=\"faq-question\">1. Ni iki gitera indwara mu bihingwa, amatungo, cyangwa amafi?</div>\r\n            <div class=\"faq-answer\">\r\n                <p>Indwara zishobora guturuka ku bibyibuha (bacteria, virus, n’ibihumyo), ibyangiza by’ibidukikije (nk’ubushyuhe bwinshi cyangwa amazi make), cyangwa kubura ingaburo zikwiye. Ikoranabuhanga ryacu ryo gusesengura amafoto rishobora kumenya ibimenyetso by’izi ndwara hakiri kare.</p>\r\n                <p class=\"farmer-name\">— Bazwa na Jean Bosco, Umuhinzi w’ibigori i Nyagatare</p>\r\n            </div>\r\n        </div>\r\n\r\n        <div class=\"faq-item\">\r\n            <div class=\"faq-question\">2. Nigute ikoranabuhanga ryo gusesengura amafoto rituma dukumira indwara vuba?</div>\r\n            <div class=\"faq-answer\">\r\n                <p>Iri koranabuhanga ryacu risuzuma amafoto y’ibimera, amatungo, cyangwa amafi kugirango ryibone ibimenyetso by’uburwayi, nk’ibikomere ku majani, ibibabi by’icyatsi, cyangwa ibihinduka ku matungo. Ibi bituma twifashisha mu gufata ingamba vuba, nk’ukoresha imiti cyangwa guhindura uburyo bwo kubaga.</p>\r\n                <p class=\"farmer-name\">— Bazwa na Marie Claire, Uborozi w’inka i Musanze</p>\r\n            </div>\r\n        </div>\r\n\r\n        <div class=\"faq-item\">\r\n            <div class=\"faq-question\">3. Ni ubuhe buryo ikoranabuhanga ryanyu rifasha mu gukumira igihombo cy’umusaruro?</div>\r\n            <div class=\"faq-answer\">\r\n                <p>Tugabanya igihombo cy’umusaruro dukoresheje isesengura ry’ibimenyetso by’uburwayi mbere y’uko bitangira kugaragara cyane. Urugero, niba indwara imaze kugaragara ku giti kimwe, dushobora gukora ingamba zikwiriye kugirango itazagera ku yindi bimera cyangwa amatungo.</p>\r\n                <p class=\"farmer-name\">— Bazwa na Pierre, Umuhinzi w’ibishyimbo i Gisenyi</p>\r\n            </div>\r\n        </div>\r\n\r\n        <div class=\"faq-item\">\r\n            <div class=\"faq-question\">4. Ni ibihe bimenyetso bikunze kugaragara ku bihingwa byanduye?</div>\r\n            <div class=\"faq-answer\">\r\n                <p>Ibi bimenyetso birimo ibibabi by’icyatsi, ibikomere ku majani, ibihinduka mu ibara ry’ibimera, cyangwa ibimera byigusha. Ikoranabuhanga ryacu rifasha kemenya ibi bimenyetso no kugaragaza inkomoko yabyo, nk’indwara z’ibihumyo cyangwa ibyangiza by’ibidukikije.</p>\r\n                <p class=\"farmer-name\">— Bazwa na Amina, Umunyamakuru w’amafi i Kivu</p>\r\n            </div>\r\n        </div>\r\n\r\n        <div class=\"video-container\">\r\n            <video controls>\r\n                <source src=\"assets/images/home.mp4\" type=\"video/mp4\">\r\n                Uru rubuga ntirushobora kwerekana video.\r\n            </video>\r\n        </div>\r\n    </div>\r\n\r\n    <script>\r\n        document.querySelectorAll(\'.faq-question\').forEach(question => {\r\n            question.addEventListener(\'click\', () => {\r\n                const answer = question.nextElementSibling;\r\n                const isActive = answer.classList.contains(\'active\');\r\n\r\n                // Close all answers\r\n                document.querySelectorAll(\'.faq-answer\').forEach(ans => {\r\n                    ans.classList.remove(\'active\');\r\n                });\r\n                document.querySelectorAll(\'.faq-question\').forEach(q => {\r\n                    q.classList.remove(\'active\');\r\n                });\r\n\r\n                // Toggle the clicked answer\r\n                if (!isActive) {\r\n                    answer.classList.add(\'active\');\r\n                    question.classList.add(\'active\');\r\n                }\r\n            });\r\n        });\r\n    </script>\r\n</body>\r\n</html>');

-- --------------------------------------------------------

--
-- Table structure for table `tblrecomanded_symptoms`
--

CREATE TABLE `tblrecomanded_symptoms` (
  `id` int(11) NOT NULL,
  `requestno` bigint(12) DEFAULT NULL,
  `recommended_symptoms` mediumtext CHARACTER SET utf8 NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblrecomanded_symptoms`
--

INSERT INTO `tblrecomanded_symptoms` (`id`, `requestno`, `recommended_symptoms`, `userEmail`, `ToDate`, `Status`, `PostingDate`, `LastUpdationDate`) VALUES
(2, 218963628, 'Dark, water-soaked spots on leaves and stems, Rapid wilting and plant decay ', 'mugisha@gmail.com', '2025-02-23', 0, '2025-02-23 11:11:17', '2025-07-06 06:51:15'),
(3, 635465359, 'Browning inside stems, White powdery spots on leaves, stems, and fruit', 'mugisha@gmail.com', '2025-02-23', 1, '2025-02-23 11:13:36', '2025-07-06 06:51:25'),
(4, 899889094, 'Poor plant growth and eventual death, Stunted plant growth ', 'patrick@gmail.com', '2025-02-23', 0, '2025-02-23 11:15:58', '2025-07-06 06:51:36'),
(5, 220511616, 'Rapid wilting and plant decay ', 'patrick@gmail.com', '2025-02-23', 1, '2025-02-23 15:13:05', '2025-07-06 06:51:47'),
(13, 290686869, 'Guhumeka nabi no gukorora k’umutungo, Umuriro mwinshi', 'mugisha1@gmail.com', '2025-07-18', 0, '2025-07-17 22:38:22', NULL),
(14, 378704209, 'Guhumeka nabi no gukorora k’umutungo', 'mugisha1@gmail.com', '2025-07-18', 0, '2025-07-17 22:38:43', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extension_worker`
--
ALTER TABLE `extension_worker`
  ADD PRIMARY KEY (`worker_id`);

--
-- Indexes for table `faostock`
--
ALTER TABLE `faostock`
  ADD PRIMARY KEY (`medecine_id`);

--
-- Indexes for table `medecine_usage`
--
ALTER TABLE `medecine_usage`
  ADD PRIMARY KEY (`usage_id`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`support_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`symptom_id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbldeseases`
--
ALTER TABLE `tbldeseases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblfarmers`
--
ALTER TABLE `tblfarmers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `EmailId` (`EmailId`);

--
-- Indexes for table `tblfound_symptoms`
--
ALTER TABLE `tblfound_symptoms`
  ADD PRIMARY KEY (`fid`);

--
-- Indexes for table `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblrecomanded_symptoms`
--
ALTER TABLE `tblrecomanded_symptoms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `extension_worker`
--
ALTER TABLE `extension_worker`
  MODIFY `worker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `faostock`
--
ALTER TABLE `faostock`
  MODIFY `medecine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medecine_usage`
--
ALTER TABLE `medecine_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `support_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbldeseases`
--
ALTER TABLE `tbldeseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tblfarmers`
--
ALTER TABLE `tblfarmers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblfound_symptoms`
--
ALTER TABLE `tblfound_symptoms`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tblrecomanded_symptoms`
--
ALTER TABLE `tblrecomanded_symptoms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
