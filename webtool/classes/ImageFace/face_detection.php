# <?php die('Access Deny');?>
# -*- coding: utf-8 -*- 

import cv2
import dlib
import json
import sys

def detect_faces(image_path):
    detector = dlib.get_frontal_face_detector()

    # 加载图像
    image = cv2.imread(image_path)

    # 转换为灰度图像
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # 用dlib进行人脸检测
    faces = detector(gray)

    face_locations = []

    # 获取每个人脸的位置坐标
    for face in faces:
        top, right, bottom, left = face.top(), face.right(), face.bottom(), face.left()
        face_locations.append((left, top, right, bottom))

    return face_locations

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python face_detection.py image_path")
        sys.exit()
    
    image_path = sys.argv[1]
    face_locations = detect_faces(image_path)

    # 将脸部位置坐标转换为JSON
    result = []
    for (left, top, right, bottom) in face_locations:
        result.append({
            'left': left,
            'top': top,
            'right': right,
            'bottom': bottom
        })
    print(json.dumps(result))