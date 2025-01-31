<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class DigitalProduct extends Product
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La lien de téléchargement ne doit pas être vide.")]
    #[Assert\Url(message: "Le lien de téléchargement doit être un URL valide.")]
    private ?string $downloadLink = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Positive(message: "La taille du fichier doit être positive.")]
    private ?int $filesize = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: [
            'pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'csv', 'odt', 'ods', 'rtf', 'wps', 'wpd', 'epub', 'mobi',
            'xml', 'xps', 'pages', 'dot', 'dotx', 'dotm',

            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp', 'svg',
            'ico', 'raw', 'heif', 'heic', 'apng', 'indd', 'ai', 'eps',

            'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'iso',
            'cab', 'arj', 'lz', 'z', 'dmg',

            'mp3', 'wav', 'aac', 'flac', 'ogg', 'm4a', 'wma', 'opus',
            'aiff', 'au', 'ra', 'mpc', 'dsd',

            'mp4', 'avi', 'mov', 'mkv', 'wmv', 'flv', 'webm', 'mpeg',
            'mpg', '3gp', 'divx', 'xvid', 'rm', 'rmvb',

            'html', 'css', 'js', 'json', 'yaml', 'md',
            'ini', 'log', 'bat', 'sh', 'ps1', 'dll', 'exe', 'bin',
            'class', 'jar', 'apk', 'rpm', 'deb', 'pl', 'py', 'rb',
            'go', 'cpp', 'c', 'h', 'swift', 'ts', 'vb', 'sql', 'asm',
            'm', 'm4', 'makefile', 'xaml', 'scss', 'sass',         'PDF', 'TXT', 'DOC', 'DOCX', 'XLS', 'XLSX', 'PPT', 'PPTX',
            'CSV', 'ODT', 'ODS', 'RTF', 'WPS', 'WPD', 'EPUB', 'MOBI',
            'XML', 'XPS', 'PAGES', 'DOT', 'DOTX', 'DOTM',

            'JPG', 'JPEG', 'PNG', 'GIF', 'BMP', 'TIFF', 'WEBP', 'SVG',
            'ICO', 'RAW', 'HEIF', 'HEIC', 'APNG', 'INDD', 'AI', 'EPS',

            'ZIP', 'RAR', '7Z', 'TAR', 'GZ', 'BZ2', 'XZ', 'ISO',
            'CAB', 'ARJ', 'LZ', 'Z', 'DMG',

            'MP3', 'WAV', 'AAC', 'FLAC', 'OGG', 'M4A', 'WMA', 'OPUS',
            'AIFF', 'AU', 'RA', 'MPC', 'DSD',

            'MP4', 'AVI', 'MOV', 'MKV', 'WMV', 'FLV', 'WEBM', 'MPEG',
            'MPG', '3GP', 'DIVX', 'XVID', 'RM', 'RMVB',

            'HTML', 'CSS', 'JS', 'JSON', 'YAML', 'MD',
            'INI', 'LOG', 'BAT', 'SH', 'PS1', 'DLL', 'EXE', 'BIN',
            'CLASS', 'JAR', 'APK', 'RPM', 'DEB', 'PL', 'PY', 'RB',
            'GO', 'CPP', 'C', 'H', 'SWIFT', 'TS', 'VB', 'SQL', 'ASM',
            'M', 'M4', 'MAKEFILE', 'XAML', 'SCSS', 'SASS', '.pdf', '.txt', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
            '.csv', '.odt', '.ods', '.rtf', '.wps', '.wpd', '.epub', '.mobi',
            '.xml', '.xps', '.pages', '.dot', '.dotx', '.dotm',

            '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.tiff', '.webp', '.svg',
            '.ico', '.raw', '.heif', '.heic', '.apng', '.indd', '.ai', '.eps',

            '.zip', '.rar', '.7z', '.tar', '.gz', '.bz2', '.xz', '.iso',
            '.cab', '.arj', '.lz', '.z', '.dmg',

            '.mp3', '.wav', '.aac', '.flac', '.ogg', '.m4a', '.wma', '.opus',
            '.aiff', '.au', '.ra', '.mpc', '.dsd',

            '.mp4', '.avi', '.mov', '.mkv', '.wmv', '.flv', '.webm', '.mpeg',
            '.mpg', '.3gp', '.divx', '.xvid', '.rm', '.rmvb',

            '.html', '.css', '.js', '.json', '.yaml', '.md',
            '.ini', '.log', '.bat', '.sh', '.ps1', '.dll', '.exe', '.bin',
            '.class', '.jar', '.apk', '.rpm', '.deb', '.pl', '.py', '.rb',
            '.go', '.cpp', '.c', '.h', '.swift', '.ts', '.vb', '.sql', '.asm',
            '.m', '.m4', '.makefile', '.xaml', '.scss', '.sass', '.PDF', '.TXT', '.DOC', '.DOCX', '.XLS', '.XLSX', '.PPT', '.PPTX',
            '.CSV', '.ODT', '.ODS', '.RTF', '.WPS', '.WPD', '.EPUB', '.MOBI',
            '.XML', '.XPS', '.PAGES', '.DOT', '.DOTX', '.DOTM',

            '.JPG', '.JPEG', '.PNG', '.GIF', '.BMP', '.TIFF', '.WEBP', '.SVG',
            '.ICO', '.RAW', '.HEIF', '.HEIC', '.APNG', '.INDD', '.AI', '.EPS',

            '.ZIP', '.RAR', '.7Z', '.TAR', '.GZ', '.BZ2', '.XZ', '.ISO',
            '.CAB', '.ARJ', '.LZ', '.Z', '.DMG',

            '.MP3', '.WAV', '.AAC', '.FLAC', '.OGG', '.M4A', '.WMA', '.OPUS',
            '.AIFF', '.AU', '.RA', '.MPC', '.DSD',

            '.MP4', '.AVI', '.MOV', '.MKV', '.WMV', '.FLV', '.WEBM', '.MPEG',
            '.MPG', '.3GP', '.DIVX', '.XVID', '.RM', '.RMVB',

            '.HTML', '.CSS', '.JS', '.JSON', '.YAML', '.MD',
            '.INI', '.LOG', '.BAT', '.SH', '.PS1', '.DLL', '.EXE', '.BIN',
            '.CLASS', '.JAR', '.APK', '.RPM', '.DEB', '.PL', '.PY', '.RB',
            '.GO', '.CPP', '.C', '.H', '.SWIFT', '.TS', '.VB', '.SQL', '.ASM',
            '.M', '.M4', '.MAKEFILE', '.XAML', '.SCSS', '.SASS'
        ],
        message: "Le type de fichier '{{ value }}' n'est pas valide. Les types acceptés."
    )]
    private ?string $filetype = null;

    public function getDownloadLink(): ?string
    {
        return $this->downloadLink;
    }

    public function setDownloadLink(string $downloadLink): static
    {
        $this->downloadLink = $downloadLink;
        return $this;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    public function setFilesize(?int $filesize): static
    {
        $this->filesize = $filesize;
        return $this;
    }

    public function getFiletype(): ?string
    {
        return $this->filetype;
    }

    public function setFiletype(?string $filetype): static
    {
        $this->filetype = $filetype;
        return $this;
    }
}
