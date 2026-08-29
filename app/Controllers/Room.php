<?php
namespace App\Controllers;
use App\Models\{RoomModel, ExamModel, ParticipantModel};
use Mpdf\Mpdf;

class Room extends BaseController
{
    public function index()
    {
        $rooms = (new RoomModel())->findAll();
        return view('room/index', ['title'=>'Manajemen Ruang', 'rooms'=>$rooms]);
    }
    public function create() { return view('room/form', ['title'=>'Tambah Ruang','room'=>null]); }
    public function store()
    {
        (new RoomModel())->insert($this->request->getPost(['nama_ruang','gedung','lantai','kapasitas','keterangan']));
        return redirect()->to(base_url('room'))->with('success','Ruang berhasil ditambahkan!');
    }
    public function edit(int $id) { return view('room/form', ['title'=>'Edit Ruang','room'=>(new RoomModel())->find($id)]); }
    public function update(int $id)
    {
        (new RoomModel())->update($id, $this->request->getPost(['nama_ruang','gedung','lantai','kapasitas','keterangan']));
        return redirect()->to(base_url('room'))->with('success','Ruang diperbarui!');
    }
    public function delete(int $id) { (new RoomModel())->delete($id); return redirect()->to(base_url('room'))->with('success','Ruang dihapus!'); }

    public function denah(int $examId, int $roomId)
    {
        $exam  = (new ExamModel())->find($examId);
        $room  = (new RoomModel())->find($roomId);
        $participants = (new ParticipantModel())->getByExamRoom($examId, $roomId);
        $html  = view('room/pdf_denah', compact('exam','room','participants'));
        $mpdf  = new Mpdf(['orientation'=>'L','margin_top'=>10,'margin_bottom'=>10,'margin_left'=>10,'margin_right'=>10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Denah_{$room['nama_ruang']}.pdf",'I');
    }
}