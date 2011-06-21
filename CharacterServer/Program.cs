using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.IO;
using System.Security.Principal;
using System.Net;
using System.Net.Sockets;
using System.Net.Security;
using Shared;
using Shared.NetWork;
using Shared.Database;
using System.Security.Cryptography;

namespace CharacterServer
{
    class Program
    {
        static public CharacterConfig Config = null;

        static void Main(string[] args)
        {
            Log.Texte("", "[CTRL + C] To shutdown the server.", ConsoleColor.Cyan);
            Log.Texte("", "-------------------------------", ConsoleColor.Cyan);
            Log.Texte("", ",---.o", ConsoleColor.Cyan);
            Log.Texte("", "`---..,---.,---.,---.,---.", ConsoleColor.Cyan);
            Log.Texte("", "    |||---'|   ||   |,---|", ConsoleColor.Cyan);
            Log.Texte("", "`---'``---'`   '`   '`---^ Core", ConsoleColor.Cyan);
            Log.Texte("", "http://twitter.com/ChameleonGeek", ConsoleColor.Cyan);
            Log.Texte("", "-------------------------------", ConsoleColor.Cyan);          

            // Loading all configs files
            ConfigMgr.LoadConfigs();
            Config = ConfigMgr.GetConfig<CharacterConfig>();

            // Loading log level from file
            if (!Log.InitLog("Configs/Characters.log", Config.LogInfo.LogsDir, Config.LogInfo.LogFile))
                WaitAndExit(Config.ShutDownTimer);

            // Starting Remoting Server
            if (!RpcServer.InitRpcServer("CharacterServer", Config.RpcKey, Config.RpcPort))
                WaitAndExit(Config.ShutDownTimer);

            // Creating Remote objects
            new AccountMgr();
            AccountMgr.AccountDB = DBManager.Start(Config.AccountsDB.Total(), ConnectionType.DATABASE_MYSQL, "Accounts");
            if (AccountMgr.AccountDB == null)
                WaitAndExit(Config.ShutDownTimer);

            new CharacterMgr();
            CharacterMgr.CharacterDB = DBManager.Start(Config.CharactersDB.Total(), ConnectionType.DATABASE_MYSQL, "Characters");
            if (CharacterMgr.CharacterDB == null)
                WaitAndExit(Config.ShutDownTimer);

            new CacheMgr();
            CacheMgr.CharacterDB = DBManager.Start(Config.CharactersDB.Total(), ConnectionType.DATABASE_MYSQL, "Characters");
            if (CacheMgr.CharacterDB == null)
                WaitAndExit(Config.ShutDownTimer);

            CharacterMgr.Instance.LoadRealms();
            CharacterMgr.Instance.LoadCreation_Names();

            // Listening Client
            if (!TCPManager.Listen<RiftServer>(Config.CharacterServerPort, "CharacterServer"))
                WaitAndExit(Config.ShutDownTimer);

            System.Console.CancelKeyPress += new ConsoleCancelEventHandler(ShutdownEvent);


            ConsoleMgr.Start();
        }

        static public void ShutdownEvent(object sender, EventArgs e)
        {
            WaitAndExit(Config.ShutDownTimer);
        }

        static public void WaitAndExit(int waittime)
        {
            Log.Info("System", String.Format("Server is shuting down within {0} seconds", waittime / 1000)); // This need fix "TCP"
            System.Threading.Thread.Sleep(waittime);
            ConsoleMgr.Stop();
            Environment.Exit(0);
        }
    }
}
