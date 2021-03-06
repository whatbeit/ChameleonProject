﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Shared;

namespace WorldServer
{
    [ISerializableAttribute((long)Opcodes.WorldChatMessage)]
    public class WorldChatMessage : ISerializablePacket
    {
        [ArrayBit(0)]
        public string ChannelName;

        [ArrayBit(2)]
        public string PlayerName;

        [ArrayBit(3)]
        public string Message;

        [Unsigned7Bit(7)]
        public long Unk1;

        [Unsigned7Bit(9)]
        public long Unk2;

        public static long CurrentMapId = 676;

        public override void OnRead(RiftClient From)
        {
            if (Message.StartsWith(".teleport"))
            {
                string CmdArgs = Message.Substring(10);
                string[] pos = CmdArgs.Split(new char[] { ' ' });

                WorldServerPositionUpdate WPos = new WorldServerPositionUpdate();

                WPos.GUID = From.Char.Id;

                WPos.Position = new List<float>();
                WPos.Position.Add(float.Parse(pos[0]));
                WPos.Position.Add(float.Parse(pos[1]));
                WPos.Position.Add(float.Parse(pos[2]));

                WPos.Orientation = new List<float>();
                WPos.Orientation.Add(0.0f);
                WPos.Orientation.Add(0.0f);
                WPos.Orientation.Add(0.0f);

                if (long.Parse(pos[3]) != CurrentMapId)
                {
                    WorldTeleport WorldPort = WorldTeleport.FromPorticulum(long.Parse(pos[3]), 1, 0, "tm_Sanctum_SanctumOfTheVigil");
                    From.SendSerialized(WorldPort);

                    WorldZoneInfo ZoneInfo = CacheMgr.Instance.GetZoneInfoCache("Capital1");
                    From.SendSerialized(ZoneInfo);

                    WorldStartingPosition StartPos = new WorldStartingPosition();
                    StartPos.MapName = "world";
                    StartPos.Position = new List<float>(WPos.Position.ToArray());

                    From.SendSerialized(StartPos);

                    ISerializablePacket PacketContainer = new ISerializablePacket();
                    WorldPositionExtra StartPos2 = new WorldPositionExtra();
                    StartPos2.MapName = "world";
                    StartPos2.MapId = 2;
                    StartPos2.Position = new List<float>(WPos.Position.ToArray());
                    StartPos2.Position2 = new List<float>(WPos.Position.ToArray());

                    PacketContainer.Opcode = (long)Opcodes.WorldStartingPositionExtra;
                    PacketContainer.AddField(0, EPacketFieldType.Packet, StartPos2);

                    From.SendSerialized(PacketContainer);
                    
                    CurrentMapId = long.Parse(pos[3]);

                    List<Cache_Info> CacheData = CacheMgr.Instance.GetBinCache(11319, false);

                    foreach (Cache_Info ci in CacheData)
                        From.SendCache(ci.Type, (uint)ci.CacheId);

                    //From.SendSerialized(WPos);

                }
                else
                    From.SendSerialized(WPos);
            }
            else
            {
                PlayerName = "Magetest";
                Unk1 = 2551;
                Unk2 = 2551;
                From.SendSerialized(this);
            }
        }
    }
}
