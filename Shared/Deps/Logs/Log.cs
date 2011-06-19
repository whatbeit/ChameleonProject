using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Shared
{
    static public class Log
    {
        static private LogConfig _config = new LogConfig();

        static private FileInfo DumpFile = null;
        static private FileStream FSDump = null;

        static public void Init(LogConfig Config)
        {
            InitInstance(Config);
        }

        static public bool SetLogLevel(string type, bool value)
        {
            switch (type.ToLower())
             {
                 case "tcp":
                     _config.Info.Tcp = value;
                     break;
                 case "debug":
                     _config.Info.Debug = value;
                     break;
                 case "dump":
                     _config.Info.Dump = value;
                     break;
                 case "notice":
                     _config.Info.Notice = value;
                     break;
                 case "error":
                     _config.Info.Error = value;
                     break;
                 case "info":
                     _config.Info.Info = value;
                     break;
                 case "successs":
                     _config.Info.Successs = value;
                     break;
                 default:
                     return false;
             }
             return true;
         }
 
         static public bool GetLogLevel(string type)
         {
             switch (type.ToLower())
             {
                 case "tcp":
                     return _config.Info.Tcp;
                 case "debug":
                     return _config.Info.Debug;
                 case "dump":
                     return _config.Info.Dump;
                 case "notice":
                     return _config.Info.Notice;
                case "error":
                     return _config.Info.Error;
                 case "info":
                     return _config.Info.Info;
                 case "successs":
                     return _config.Info.Successs;
             }
             return false;
         }
 
         static public bool InitLog(string LogConf, string LogsDir, string fileName)
         {
            try
            {
                LogConfig Conf = new LogConfig(0);
                Conf.FileName = fileName;
                Conf.LogFolder = LogsDir;

                if (LogConf.Length > 0)
                    Conf.LoadInfoFromFile(LogConf);

                Log.Init(Conf);
            }
            catch (Exception e)
            {
                Log.Error("InitLog", "Error : " + e.ToString());
                return false;
            }

            Log.Notice("InitLog", "Logger initialized");
            return true;
        }

        static public void InitInstance(LogConfig Config)
        {
            try
            {
                if (Config == null)
                    Config = new LogConfig();

                if (!Config.LogFolder.StartsWith("/"))
                    Config.LogFolder = "/" + Config.LogFolder;

                string FileDir = Directory.GetCurrentDirectory() + Config.LogFolder;
                string BackDir = Directory.GetCurrentDirectory() + Config.LogFolder + "/Backup";

                try
                {
                    Directory.CreateDirectory(FileDir);
                    Directory.CreateDirectory(BackDir);
                }
                catch (Exception)
                {

                }

                FileDir += "/" + Config.FileName;
                BackDir += "/" + Config.FileName + "." + DateTime.Now.Hour + "h." + DateTime.Now.Minute + "m." + DateTime.Now.Second + "s";

                if (DumpFile == null)
                {
                    DumpFile = new FileInfo(FileDir);
                    if (DumpFile.Exists)
                        DumpFile.MoveTo(BackDir);

                    DumpFile = new FileInfo(FileDir);

                    if (FSDump != null)
                        FSDump.Close();

                    FSDump = DumpFile.Create();
                }

                if (Config != null)
                    _config = Config;

            }
            catch (Exception)
            {
                lock (Console.Out)
                    Console.WriteLine("Log : Log file already in use.");

                if (Config != null)
                    Config.Info.Dump = false;
            }
        }

        public static void Texte(string name, string message, ConsoleColor Color)
        {
            lock (_config)
            {
                string Texte = "[" + DateTime.Now.ToString("HH:mm:ss") + "] " + name + " : " + message;

                lock (Console.Out)
                {
                    Console.BufferHeight = Console.WindowWidth - 20;
                    Console.ForegroundColor = Color;
                    Console.WriteLine(Texte);
                    Console.ForegroundColor = ConsoleColor.White;
                }

                if (DumpFile != null && FSDump != null)
                {
                    byte[] info = new UTF8Encoding(true).GetBytes(Texte+"\n");
                    FSDump.Write(info, 0, info.Length);
                    FSDump.Flush();
                }

            }
        }

        public static void Enter() // Saute une ligne
                                   // Step one line
        {
            lock (_config)
            {
                lock (Console.Out)
                {
                    Console.WriteLine("");
                }
            }
        }

        public static void Info(string name,string message)
        {
            if (_config.Info.Info)
                Texte("I "+name, message, ConsoleColor.White);
        }

        public static void Success(string name, string message)
        {
            if (_config.Info.Successs)
                Texte("S " + name, message, ConsoleColor.Green);
        }

        public static void Notice(string name, string message)
        {
            if (_config.Info.Notice)
                Texte("N " + name, message, ConsoleColor.Yellow);
        }

        public static void Error(string name, string message)
        {
            if (_config.Info.Error)
                Texte("E " + name, message, ConsoleColor.Red);
        }

        public static void Debug(string name, string message)
        {
            if (_config.Info.Debug)
                Texte("D " + name, message, ConsoleColor.Blue);
        }

        public static void Dump(string name, string message)
        {
            if (_config.Info.Dump)
                Texte("D " + name, message, ConsoleColor.Gray);
        }

        public static void Tcp(string name, byte[] dump, int start, int len)
        {
            if (_config.Info.Tcp)
                Texte("P " + name, Hex(dump, start, len), ConsoleColor.Gray);
        }

        public static void Dump(string name, byte[] dump , int start , int len)
        {
            if (_config.Info.Dump)
                Texte("U " + name, Hex(dump,start,len), ConsoleColor.Gray);
        }

        public static void Compare(string Name, byte[] First, byte[] Second)
        {
            if (_config.Info.Dump)
                return;

            if (First.Length != Second.Length)
                Log.Error("Name", "First.Length(" + First.Length + ") != Second.Length(" + Second.Length + ")");

            StringBuilder hex = new StringBuilder();

            for (int i = 0; i < Math.Max(First.Length, Second.Length); i += 16)
            {
                hex.Append("\n");

                bool LastDiff = false;
                for (int j = 0; j < 16; ++j)
                {
                    if (j + i < First.Length)
                    {
                        if (j + i < Second.Length)
                        {
                            if (First[j + i] != Second[j + i] && !LastDiff)
                            {
                                LastDiff = true;
                                hex.Append("[");
                            }
                            else if (First[j + i] == Second[j + i] && LastDiff)
                            {
                                LastDiff = false;
                                hex.Append("]");
                            }
                        }
                        else if (LastDiff)
                        {
                            LastDiff = false;
                            hex.Append("]");
                        }
                          

                        byte val = First[j + i];
                        //hex.Append(" ");
                        hex.Append(First[j + i].ToString("X2"));
                        if (j == 3 || j == 7 || j == 11)
                            hex.Append("");
                    }
                    else
                    {
                        hex.Append("  ");
                    }
                }
                if (LastDiff)
                {
                    LastDiff = false;
                    hex.Append("]");
                }

                hex.Append(" || ");

                LastDiff = false;
                for (int j = 0; j < 16; ++j)
                {
                    if (j + i < Second.Length)
                    {
                        if (j + i < First.Length)
                        {
                            if (First[j + i] != Second[j + i] && !LastDiff)
                            {
                                LastDiff = true;
                                hex.Append("[");
                            }
                            else if (First[j + i] == Second[j + i] && LastDiff)
                            {
                                LastDiff = false;
                                hex.Append("]");
                            }
                        }
                        else if (LastDiff)
                        {
                            LastDiff = false;
                            hex.Append("]");
                        }

                        byte val = Second[j + i];
                        //hex.Append(" ");
                        hex.Append(Second[j + i].ToString("X2"));
                        if (j == 3 || j == 7 || j == 11)
                            hex.Append("");
                    }
                    else
                    {
                        if (LastDiff)
                        {
                            LastDiff = false;
                            hex.Append("]");
                        }
  
                        hex.Append("  ");
                    }
                }
            }

            Texte("C " + Name, hex.ToString(), ConsoleColor.Gray);
        }

        public static string Hex(byte[] dump, int start, int len)
        {
            var hexDump = new StringBuilder();

            try
            {
                int end = start + len;
                for (int i = start; i < end; i += 16)
                {
                    StringBuilder text = new StringBuilder();
                    StringBuilder hex = new StringBuilder();
                    hex.Append("\n");

                    for (int j = 0; j < 16; j++)
                    {
                        if (j + i < end)
                        {
                            byte val = dump[j + i];
                            hex.Append(" ");
                            hex.Append(dump[j + i].ToString("X2"));
                            if (j == 3 || j == 7 || j == 11)
                                hex.Append(" ");
                            if (val >= 32 && val <= 127)
                            {
                                text.Append((char)val);
                            }
                            else
                            {
                                text.Append(".");
                            }
                        }
                        else
                        {
                            hex.Append("   ");
                            text.Append("  ");
                        }
                    }
                    hex.Append("  ");
                    hex.Append("//"+text.ToString());
                    hexDump.Append(hex.ToString());
                }
            }
            catch (Exception e)
            {
                Log.Error("HexDump", e.ToString());
            }

            return hexDump.ToString();
        }
    }
}
